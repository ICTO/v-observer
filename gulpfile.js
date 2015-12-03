var elixir = require('laravel-elixir');
var rmdir = require('rmdir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir.extend('remove', function(path) {
    new elixir.Task('remove', function() {
        rmdir(path, function ( err, dirs, files ){});
    });
});

elixir(function(mix) {
    // stylesheets
    mix.sass('app.scss', 'resources/assets/generated_css');

    mix.styles([
            "../../../bower_components/Materialize/dist/css/materialize.css",
            "../generated_css/app.css"
       ], 'public/stylesheets/main.css');

    // scripts
    mix.scripts(['../../../bower_components/jquery/dist/jquery.js',
                 '../../../bower_components/Materialize/dist/js/materialize.js',
                 '../../../bower_components/moment/min/moment-with-locales.js',
                 '../../../bower_components/noty/js/noty/packaged/jquery.noty.packaged.min.js',
                 '../javascript/main.js'
       ], 'public/javascript/main.js');

    mix.scripts(['../javascript/MultipleChoiceQuestion.js'
], 'public/javascript/MultipleChoiceQuestion.js');

    // Add version
    //mix.version(["public/javascript/main.js", "public/stylesheets/main.css"]);

    // copy fonts
    mix.copy('bower_components/Materialize/dist/font', 'public/font');

    // remove unused folders
    mix.remove(__dirname + "/resources/assets/generated_css");

    // remove this folders when using version
    //mix.remove(__dirname + "/public/javascript");
    //mix.remove(__dirname + "/public/stylesheets");
});
