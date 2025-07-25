<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Guid\Guid;

class FailedJob extends Model
{
    protected $table = 'failed_jobs';

    protected $fillable = ['uuid', 'connection', 'queue', 'payload', 'exception', 'failed_at'];

    // Configuração para usar timestamps
    public $timestamps = true;  // Garante que os campos created_at e updated_at sejam utilizados.

    protected static function boot()
    {
        parent::boot();

        // Se o UUID não for fornecido, gera um UUID v4
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Guid::uuid4();  // Gerando UUID v4
            }
        });
    }
}



\Mail::raw('Test message', function ($message) {
    $message->to('miguel.palma@jf-parquedasnacoes.pt')
            ->subject('Test Email');       
});




$email = new App\Mail\CheckinCheckoutNotification(
    $utente,                // O utente carregado
    $manutencao,            // O incidente

);





teste tinker

$utente = App\Models\Asset::find(838);
$utilizadorBackoffice = App\Models\User::find(642);
$responsavel = App\Models\Asset::find(840);
$manutencao = App\Models\AssetMaintenance::find(297);

$email = new App\Mail\CheckinCheckoutNotification(
    $utente,
    $utilizadorBackoffice,
    $manutencao,
    'checkout',
    $responsavel,
    'João Silva',
    '123456789'
);

Mail::to('miguel.palma@jf-parquedasnacoes.pt')->send($email);
