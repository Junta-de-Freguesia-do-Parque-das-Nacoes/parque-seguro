<?php

namespace App\Console\Commands;

use App\Helpers\Helper;
use Illuminate\Console\Command;
use App\Models\User;
use Laravel\Passport\TokenRepository;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Support\Facades\DB;

class GeneratePersonalAccessToken extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'snipeit:make-api-key 
                        {--user_id= : The ID of the user to create the token for.}
                        {--name= : The name of the new API token}
                        {--key-only : Only return the value of the API key}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This console command allows you to generate Personal API tokens to be used with the Parque Seguro JSON REST API on behalf of a user.';


    /**
     * The token repository implementation.
     *
     * @var \Laravel\Passport\TokenRepository
     */
    protected $tokenRepository;


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TokenRepository $tokenRepository, ValidationFactory $validation)
    {
        $this->validation = $validation;
        $this->tokenRepository = $tokenRepository;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $accessTokenName = $this->option('name');
        if ($accessTokenName=='') {
            $accessTokenName = 'CLI Auth Token';
        }

        if ($this->option('user_id')=='') {
            return $this->error('ERROR: user_id cannot be blank.');
        }

        if ($user = User::find($this->option('user_id'))) {

            $createAccessToken = $user->createToken($accessTokenName)->accessToken;

            if ($this->option('key-only')) {
                $this->info($createAccessToken);

            } else {

                $this->warn('Your API Token has been created. Be sure to copy this token now, as it will not be accessible again.');

                if ($token = DB::table('oauth_access_tokens')->where('user_id', '=', $user->id)->where('name','=',$accessTokenName)->orderBy('created_at', 'desc')->first()) {
                    $this->info('API Token ID: '.$token->id);
                }

                $this->info('API Token User: '.$user->present()->fullName.' ('.$user->username.')');
                $this->info('API Token Name: '.$accessTokenName);
                $this->info('API Token: '.$createAccessToken);
            }
        } else {
           return $this->error('ERROR: Invalid user. API key was not created.');
        }




    }
}
