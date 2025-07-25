{{ trans('mail.reset_password') }} 
<a href="{{ $link = url('password/reset', $token).'?email='.urlencode($user->getEmailForPasswordReset()) }}"> 
    Clique aqui para redefinir a sua palavra-passe
</a>
