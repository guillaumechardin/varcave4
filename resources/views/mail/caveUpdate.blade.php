@props([
    'data',
])

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        /* Styles inline pour compatibilité email */
        body {
            font-family: "Helvetica", "Arial", sans-serif;
            background-color: #f5f5f5;
            color: #4a4a4a;
            padding: 2rem;
        }
        .container {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 6px;
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 2px 3px rgba(10, 10, 10, 0.1);
        }
        h1 {
            font-size: 1.8rem;
            color: #363636;
        }
        p {
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        .button {
            display: inline-block;
            background-color: #00d1b2;
            color: #fff !important;
            padding: 0.75rem 1.25rem;
            font-weight: 600;
            text-decoration: none;
            border-radius: 4px;
        }
        .button:hover {
            background-color: #00b89c;
        }
        .footer {
            text-align: center;
            font-size: 0.9rem;
            color: #7a7a7a;
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="title-wrapper">
		  <div style="display:flex; align-items:center;">
			<img src="{{ $message->embed(public_path('img/logo_mail_pw-reset_100x100.png')) }}" style="max-height:100px; margin-right:15px;">
			{{-- <h1 style="margin:0;">{{ __('reset-password.subject') }}</h1> --}}
		  </div>
		</div>

        <p>{!! nl2br(__('varcave.email.caveUpdate.welcomeTxt', [ 'name' => $data['name'] ])) !!}</p>
        
        <p>{!! nl2br(e($data['body'])) !!}</p>

        <p>
            <div>
                {{ __('varcave.email.caveUpdate.default_link', ['caveName' => $data['caveName']]) }}
            </div>
            <div>
                <a href="{{ route('varcave.caves.show', [ 'uuid' => $data['uuid'] ]) }}">{{ route('varcave.caves.show', [ 'uuid' => $data['uuid'] ]) }}</a>
            </div>
        </p>
        

        <div class="footer">
            {{ env('APP_NAME') }}
        </div>
    </div>

    <h1></h1>
    
    
</body>
</html>