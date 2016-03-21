@extends('layouts.fullcenter')

@section('content')
<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12 center-align valign" style="heigth:100%">
                <img src="/images/logo_ugent.jpg" height="200" />
                <p>Video observation application developed by the University of Ghent with the support of "Scientific Research Flanders".<br/>
                Video observatie applicatie ontwikkeld door de Universiteit van Gent met de ondersteuning van "Wetenschappelijk Onderzoek Vlaanderen".</p>
                <a class="waves-effect waves-light btn" href="{{ action('Auth\AuthController@getLogin') }}">Start</a>
            </div>
        </div>
    </div>
</div>
@endsection
