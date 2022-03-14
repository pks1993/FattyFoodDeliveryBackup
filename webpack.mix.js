const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js').vue();



mix.scripts([
	'resources/plugins/jquery/jquery.min.js',
	'resources/plugins/bootstrap/js/bootstrap.bundle.min.js',
	'resources/plugins/datatables/jquery.dataTables.js',
	'resources/plugins/datatables-bs4/js/dataTables.bootstrap4.js',
	'resources/plugins/chart.js/Chart.min.js',
	'resources/dist/js/adminlte.min.js',
	'resources/dist/js/demo.js',

	],'public/js/admin.js')
	.styles([
	'resources/dist/css/adminlte.min.css',
	'resources/plugins/datatables-bs4/css/dataTables.bootstrap4.css',
	'resources/css/font-awesome-4.7.0/css/font-awesome.min.css',
	'resources/plugins/icheck-bootstrap/icheck-bootstrap.min.css',
	'resources/css/style.css',
    'resources/css/mmfonts.css',



	],'public/css/admin.css');

