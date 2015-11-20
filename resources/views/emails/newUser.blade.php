Hello {{ $new_user->name }},

You received an invitation from {{ $user->name }}.

If you want to activate your account, you need to choose a new password on {{ url(action('Auth\PasswordController@getReset',$token)) }}.

@if( Config::get('cas.cas_hostname') )
If you have a CAS account and the username is added to the account by your inviter, you can skip the password setup link and go immediately to {{ url(action('Auth\AuthController@getCas')) }}
@endif
