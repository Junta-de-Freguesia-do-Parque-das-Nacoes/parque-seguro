<?php

use App\Http\Controllers\Account;
use App\Http\Controllers\ActionlogController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CompaniesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentsController;
use App\Http\Controllers\DepreciationsController;
use App\Http\Controllers\GroupsController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\ImportsController;
use App\Http\Controllers\LabelsController;
use App\Http\Controllers\LocationsController;
use App\Http\Controllers\ManufacturersController;
use App\Http\Controllers\ModalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StatuslabelsController;
use App\Http\Controllers\SuppliersController;
use App\Http\Controllers\ViewAssetsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Livewire\Importer;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\CheckInOutController;
use App\Http\Controllers\ResponsavelController;
use App\Http\Controllers\CriancaController;
use App\Http\Controllers\HistoryController;
use App\Exports\HistoryExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\Assets\AssetsController;
use App\Http\Controllers\PreferencesController;
use App\Http\Controllers\QrCodeEmailController;
use App\Models\Asset;
use App\Models\User;
use App\Http\Controllers\EmailLogsController;
use App\Http\Controllers\ScannerController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\ResponsavelAssociacaoController;
use App\Http\Controllers\ImportacaoController;
use App\Http\Controllers\ProgramaController;
use App\Http\Controllers\EeAuthController;
use App\Http\Controllers\EeResponsavelController;
use App\Http\Controllers\NotificacaoBackofficeController;
use App\Http\Controllers\CartaoController;
use App\Mail\CheckoutNotificationMail;


Route::get('/responsaveis/foto/{filename}', function ($filename) {
    $path = public_path("uploads/responsaveis/fotos/{$filename}");

    if (!file_exists($path)) {
        abort(404);
    }

    // Proteção: só utilizadores autenticados podem ver a imagem
    if (!Auth::check()) {
        abort(403);
    }

    return Response::file($path);
})->middleware('auth')->name('responsaveis.foto');


Route::get('/assets/foto/{filename}', function ($filename) {
    $path = public_path("uploads/assets/{$filename}");

    if (!file_exists($path)) {
        abort(404);
    }

    // Proteção: apenas utilizadores autenticados podem visualizar
    if (!Auth::check()) {
        abort(403);
    }

    return Response::file($path);
})->middleware('auth')->name('assets.foto');


Route::get('/responsaveis/documento/{filename}', function ($filename, Request $request) {
    // ✅ Verifica se o utilizador está autenticado
    if (!auth()->check()) {
        abort(403, 'Acesso negado');
    }

    // 🔒 Caminho seguro do documento
    $path = public_path("uploads/responsaveis/documentos/{$filename}");

    // ❌ Se o ficheiro não existir, retorna erro 404
    if (!file_exists($path)) {
        abort(404, 'Documento não encontrado');
    }

    // ✅ Retorna o ficheiro como resposta
    return response()->file($path);
})->name('responsaveis.documento')->middleware('auth');


Route::post('/cartoes/enviar-selecionados', [App\Http\Controllers\CartaoController::class, 'enviarSelecionados']) ->name('cartoes.enviar.selecionados');



// Atualização de preferências via modal
Route::post('/preferences/{id}', [PreferencesController::class, 'update'])->name('preferences.update');

// Página de preferências via token
Route::get('/preferences/{id}/{token}', [PreferencesController::class, 'editWithToken'])->name('preferences.token.edit');


// Enviar código de verificação
Route::post('/preferences/{id}/send-code', [PreferencesController::class, 'sendVerificationCode'])->name('preferences.sendCode');

// Acesso livre (login/código)
Route::get('/ee/login', [EeAuthController::class, 'showLoginForm'])->name('ee.login');
Route::post('/ee/logout', [EeAuthController::class, 'logout'])->name('ee.logout');
Route::get('/ee/logout-redirect', function () {
    return view('ee.logout-redirect');
})->name('ee.logout.redirect');


Route::post('/ee/send-code', [EeAuthController::class, 'enviarCodigo'])
    ->middleware('throttle:5,1')
    ->name('ee.sendCode');

Route::post('/ee/verificar-codigo', [EeAuthController::class, 'verificarCodigo'])
    ->middleware('throttle:5,1')
    ->name('ee.verificar-codigo');

Route::get('/ee/codigo', [EeAuthController::class, 'mostrarFormCodigo'])->name('ee.mostrar-form-codigo');
Route::post('/ee/aceitar-rgpd', function () {
    session(['rgpd_consent_ee' => true]);
    return response()->json(['success' => true]);
})->name('ee.aceitar-rgpd');

Route::get('/ee/reenviar-codigo', [EeAuthController::class, 'reenviarCodigo'])
    ->middleware('throttle:3,1')
    ->name('ee.reenviar-codigo');

// Protegidas com middleware
Route::middleware(['ee.auth'])->group(function () {
    Route::get('/ee/dashboard', [EeAuthController::class, 'dashboard'])->name('ee.gestao');

    // Utente
    Route::get('/ee/utente/{id}/editar', [EeAuthController::class, 'editarUtente'])->name('ee.utente.editar');
    Route::put('/ee/utente/{id}', [EeAuthController::class, 'updateUtente'])->name('ee.utente.update');
    Route::post('/ee/utente/{id}/upload-anexo', [EeAuthController::class, 'uploadAnexo'])->name('ee.utente.uploadAnexo');
    Route::post('/ee/utente/{id}/foto', [EeAuthController::class, 'atualizarFoto'])->name('ee.utente.atualizarFoto');
    
    Route::post('/ee/utente/{id}/nota', [EeAuthController::class, 'guardarNota'])->name('ee.utente.guardarNota');
    Route::delete('/ee/utente/{id}/nota/{notaId}', [EeAuthController::class, 'eliminarNota'])->name('ee.utente.eliminarNota');
    Route::get('/ee/utente/{id}/ficheiro/{filename}', [EeAuthController::class, 'verFicheiro'])->where('filename', '.*')->name('ee.utente.verFicheiro');
    Route::post('/ee/utente/{id}/preferencia-ajax', [EeAuthController::class, 'atualizarPreferenciaAjax']);
    Route::get('/ee/utente/{id}/historico', [EeAuthController::class, 'historicoUtente'])->name('ee.utente.historico');
    Route::get('/ee/utente/{id}/qr', [App\Http\Controllers\EeAuthController::class, 'mostrarQr'])
     ->name('ee.utente.qr');

    // Responsável
    Route::get('/ee/responsavel/fotos/{filename}', [EeResponsavelController::class, 'verFoto'])->name('ee.responsavel.foto')->where('filename', '.*');
    Route::get('/ee/responsavel/fotos/{filename}', [EeResponsavelController::class, 'verFoto'])->name('ee.responsavel.foto');
    Route::post('/ee/responsavel/{id}/remover', [EeResponsavelController::class, 'removerResponsavel'])->name('ee.responsavel.remover');
    Route::post('/ee/responsavel/{id}/foto', [EeResponsavelController::class, 'atualizarFoto'])->name('ee.responsavel.atualizarFoto');

    // Responsável (Gestão)
    Route::post('/ee/responsavel/autorizar-ajax', [EeAuthController::class, 'autorizarResponsavelAjax'])->name('ee.responsavel.autorizar.ajax');
    Route::get('/ee/responsaveis/verificar', [EeAuthController::class, 'verificarResponsavel'])->name('ee.responsaveis.verificar');
    Route::post('/ee/responsaveis/criar', [EeAuthController::class, 'criarERelacionarResponsavel'])->name('ee.responsaveis.criar');
    Route::post('/ee/responsaveis/desassociar', [EeAuthController::class, 'desassociarResponsavel'])->name('ee.desassociar.responsavel');
    Route::get('/ee/responsaveis/ligacoes', [EeAuthController::class, 'verTodosResponsaveis'])->name('ee.responsaveis.ligacoes');
    Route::post('/ee/associacao/atualizar', [EeResponsavelController::class, 'atualizarAssociacao'])->name('ee.associacao.atualizar');
    Route::post('/ee/responsaveis/criar-global', [EeResponsavelController::class, 'criarGlobal'])->name('ee.responsaveis.criar-global');
    Route::post('/ee/enviar-qrcodes', [EeAuthController::class, 'enviarQRCodes'])->name('ee.enviar.qrcodes');
    


    Route::post('/ee/responsavel/{id}/atualizar', [EeResponsavelController::class, 'atualizarDados'])->name('ee.responsavel.atualizarDados');

    // Página de gestão com edição/criação
    Route::get('/ee/utente/{id}/responsaveis/gerir', [EeAuthController::class, 'gerirResponsaveis'])->name('ee.gerir-responsaveis');

    // Página só de visualização simples
    Route::get('/ee/utente/{id}/responsaveis', [EeAuthController::class, 'responsaveis'])->name('ee.responsaveis');
    // Rota de exibição leve (ex: últimos 3 autorizados)
    Route::get('/ee/utente/{id}/responsaveis-visao-rapida', [EeAuthController::class, 'responsaveisVisaoRapida'])->name('ee.responsaveis.visao-rapida');

    // Logout
    Route::post('/ee/logout', [EeAuthController::class, 'logout'])->name('ee.logout');
});

// Foto do utente protegida
Route::middleware(['ee.auth'])->get('/ee/assets/foto/{filename}', [EeAuthController::class, 'verFoto'])->name('ee.utente.foto');








// Verificar e salvar preferências via token
Route::post('/preferences/{id}/{token}', [PreferencesController::class, 'verifyAndUpdateWithToken'])->name('preferences.token.update');
Route::get('/responsaveis/buscar', [ResponsavelController::class, 'buscarResponsavel'])->name('responsaveis.buscar');

// Rota para associar um responsável a um utente
Route::middleware(['auth', 'checkGroup:monitores'])->group(function () {
    Route::get('/utentes/{utenteId}/responsaveis/create-associado', [ResponsavelAssociacaoController::class, 'showCreateAssociadoForm'])->name('responsaveis.createAssociadoForm');
    Route::post('/utentes/{utenteId}/responsaveis/create-associado', [ResponsavelAssociacaoController::class, 'createAssociado'])->name('responsaveis.createAssociado');
    Route::post('/responsaveis/{utenteId}/associar', [ResponsavelAssociacaoController::class, 'associar'])->name('responsaveis.associar');
    Route::get('/importacao', [ImportacaoController::class, 'index'])->name('importacao.index');
Route::post('/importacao', [ImportacaoController::class, 'index']);
Route::post('/importacao/importar', [ImportacaoController::class, 'importar'])->name('importacao.importar');
Route::get('/programas/gestao', [\App\Http\Controllers\ProgramaController::class, 'index'])->name('programas.gestao');
Route::post('/programas/gestao', [\App\Http\Controllers\ProgramaController::class, 'limpar'])->name('programas.gestao.post');
// Blade de gestão das opções dos programas
Route::get('/programas/opcoes/{field_id}', [ProgramaController::class, 'gerirOpcoes'])->name('programas.opcoes');
Route::put('/programas/opcoes/{field_id}', [ProgramaController::class, 'atualizarOpcoes'])->name('programas.opcoes.atualizar');
Route::get('/programas/exportar', [ProgramaController::class, 'exportar'])->name('programas.exportar');
    

    
});



// Rotas para responsáveis SEM estar dentro de "utentes"
Route::middleware(['auth', 'checkGroup:monitores'])->prefix('responsaveis')->group(function () {
    Route::get('/', [ResponsavelController::class, 'listar'])->name('responsaveis.listar'); // Lista todos os responsáveis
    Route::get('/create', [ResponsavelController::class, 'create'])->name('responsaveis.create'); // Formulário de criação
    Route::post('/store', [ResponsavelController::class, 'store'])->name('responsaveis.store'); // Criar responsável
    Route::get('/{responsavelId}', [ResponsavelController::class, 'show'])->name('responsaveis.show'); // Exibir responsável
    Route::get('/{responsavelId}/edit', [ResponsavelController::class, 'edit'])->name('responsaveis.edit'); // Editar responsável
    Route::put('/{responsavelId}', [ResponsavelController::class, 'update'])->name('responsaveis.update'); // Atualizar responsável
    Route::delete('/{responsavelId}', [ResponsavelController::class, 'destroy'])->name('responsaveis.destroy'); // Remover responsável
    Route::post('/atualizar-associacao', [ResponsavelController::class, 'atualizarAssociacao'])->name('responsaveis.atualizarAssociacao');
    Route::post('/responsaveis/remover-associacao', [ResponsavelController::class, 'removerAssociacao'])->name('responsaveis.removerAssociacao');
    Route::post('/novo', [ResponsavelController::class, 'storeNovoResponsavel'])->name('responsaveis.storeNovo');
    Route::delete('/{responsavelId}/remover-completamente', [ResponsavelController::class, 'removerCompletamente'])->name('responsaveis.removerCompletamente');
    Route::post('/associar/{utenteId}', [ResponsavelController::class, 'storeAssociado'])->name('responsaveis.storeAssociado');
    


    


    // Notas e documentos (mantendo a organização)
    Route::post('/{responsavelId}/upload-documento', [ResponsavelController::class, 'uploadDocumento'])->name('responsaveis.uploadDocumento');
    Route::post('/{responsavelId}/notas/adicionar', [ResponsavelController::class, 'adicionarNota'])->name('responsaveis.adicionarNota');
    Route::get('/{responsavelId}/notas', [ResponsavelController::class, 'carregarNotas'])->name('responsaveis.carregarNotas');
    Route::delete('/{responsavelId}/removeFoto', [ResponsavelController::class, 'removeFoto'])->name('responsaveis.removeFoto');
    Route::delete('/{responsavelId}/documentos/{documentoId}', [ResponsavelController::class, 'removeDocumento'])->name('documentos.destroy');
    Route::post('/adicionar-utente', [ResponsavelController::class, 'adicionarUtente'])->name('responsaveis.adicionarUtente');
    Route::get('/buscar-utentes-nao-associados/{responsavelId}', [ResponsavelController::class, 'buscarUtentesNaoAssociados'])->name('responsaveis.buscarUtentesNaoAssociados');

});

// Rotas para responsáveis RELACIONADOS A UM UTENTE
Route::middleware(['auth', 'checkGroup:monitores'])->prefix('utentes/{utenteId}/responsaveis')->group(function () {
    Route::get('/', [ResponsavelController::class, 'index'])->name('responsaveis.index'); // Lista responsáveis de um utente específico

    Route::get('/search', [ResponsavelController::class, 'search'])->name('responsaveis.search'); // Pesquisar responsáveis de um utente
   
});

    
 


Route::middleware(['auth', 'checkGroup:monitores'])->group(function () {
    // Rota para o histórico de check-ins e check-outs dos utentes
    Route::get('/{id}/historico', [CheckInOutController::class, 'historico'])->name('checkout.history');

    // Rota para armazenar crianças relacionadas a um utente
    Route::post('/{utente}/criancas', [CriancaController::class, 'store'])->name('criancas.store');

    // Rota para exportar o histórico dos utentes em formato Excel
    Route::get('/{id}/historico/export', function ($id) {
        $startDate = request('start_date');
        $endDate = request('end_date');
        return Excel::download(new HistoryExport($id, $startDate, $endDate), 'historico.xlsx');
    })->name('history.export.excel');

    // Rota para exibir todos os assets
    Route::get('/assets', [AssetsController::class, 'index'])->name('assets.index');

    // Rota para o scanner de QR Code
    Route::get('/qr-code-scanner', [ScannerController::class, 'index'])->name('qr-code-scanner');
});



Route::middleware(['auth', 'checkGroup:monitores'])->group(function () {
    // Página de edição de preferências
    Route::get('/preference/{id}/edit', [PreferencesController::class, 'edit'])->name('preferences.edit');
    
    // Atualizar preferências (compatível com POST+_method=PUT e PUT direto)
    Route::match(['POST', 'PUT'], '/preference/{id}/update', [PreferencesController::class, 'update'])->name('preferences.update.single');
	Route::get('/hardware/{id}/sendQrEmail', [QrCodeEmailController::class, 'sendQrEmail'])->name('hardware.sendQrEmail');
	Route::get('/historico', [HistoryController::class, 'showAllHistory'])->name('history.all');
	Route::get('/historico/export/excel', [HistoryController::class, 'exportAllToExcel'])->name('history.export.all.excel');
	Route::get('/email-logs', [EmailLogsController::class, 'index'])->name('email-logs.index');
	Route::get('/email-logs/export', [EmailLogsController::class, 'export'])->name('email-logs.export');
	Route::get('/email-logs/autocomplete', [EmailLogsController::class, 'autocomplete'])->name('email-logs.autocomplete');
	Route::get('/send-bulk-emails', [QrCodeEmailController::class, 'sendBulkEmails'])->name('send.bulk.qr');
    Route::post('/send.bulk.qr/filtrados', [QrCodeEmailController::class, 'sendFilteredEmails'])->name('send.bulk.qr.filtered');
	Route::get('/autocomplete/utentes', function (Request $request) {
    $search = $request->query('q');
    $results = DB::table('assets')
        ->where('name', 'LIKE', "%{$search}%")
        ->orWhere('asset_tag', 'LIKE', "%{$search}%")
        ->select('id', 'name', 'asset_tag') // Certifique-se de incluir asset_tag
        ->take(10)
        ->get();
            return response()->json($results);
            })->name('autocomplete.utentes');
            Route::get('/mapa-presencas', [HistoryController::class, 'mapaPresencasMensal'])->name('mapa.presencas');
            Route::get('/mapa-presencas/exportar', [HistoryController::class, 'exportarMapa'])->name('mapa.presencas.exportar');

     Route::get('/notificacoes', [NotificacaoBackofficeController::class, 'index'])->name('notificacoes.index');
    Route::post('/notificacoes/{id}/marcar-lida', [NotificacaoBackofficeController::class, 'marcarComoLida'])->name('notificacoes.marcarLida');
Route::post('/notificacoes/marcar-todas-lidas', [NotificacaoBackofficeController::class, 'marcarTodasComoLidas'])->name('notificacoes.marcarTodasLidas');
    Route::get('/notificacoes/nao-lidas/count', [NotificacaoBackofficeController::class, 'contagem'])->name('notificacoes.contagem');
    Route::get('/notificacoes/{id}/lida', [NotificacaoBackofficeController::class, 'marcarComoLidaRedirect'])
    ->name('notificacoes.marcarLidaRedirect');


Route::get('/notificacoes/recentes', function () {
    $notificacoes = auth()->user()
        ->notificacoes()
        ->withPivot('lida')
        ->orderBy('notificacoes_backoffice.created_at', 'desc')
        ->take(5)
        ->get();

    return response()->json($notificacoes->map(function ($n) {
        return [
            'id' => $n->id,
            'mensagem' => strip_tags($n->mensagem),
            'html' => $n->mensagem,
            'lida' => $n->pivot->lida,
            'data' => $n->created_at->format('d/m/Y H:i'),
        ];
    }));
})->name('notificacoes.recentes');


    });



// Rotas adicionais que exigem autenticação
Route::middleware('auth')->group(function () {

    // Rota para scanner de QR code
	Route::get('/qr-code-scanner', [ScannerController::class, 'index'])->name('qr-code-scanner');
	Route::get('/presence/details', [ScannerController::class, 'showPresenceDetails'])->name('presence.details');
	Route::get('/presencas-ausencias', [ScannerController::class, 'showPresenceAbsences'])->name('presencas.ausencias');
    

	
    Route::middleware(['auth'])->group(function () {

        Route::get('/app-download', function () {
            return view('apk.download');
        })->name('app-download'); // <-- Faltava este "name"
    
        Route::get('/download-apk', function () {
            return response()->download(public_path('apk/app-parqueseguro.apk'));
        })->name('download-apk');
    
        Route::get('/qr-code-apk', function () {
            return \QrCode::format('png')
                ->size(300)
                ->generate(route('download-apk'), \Illuminate\Support\Facades\Response::make('', 200, ['Content-Type' => 'image/png']));
        })->name('qr-code-apk');


        
    });
    
    Route::middleware('auth')->group(function () {
        Route::post('/settings/save-label-template', [SettingsController::class, 'saveLabelTemplate'])
            ->name('settings.saveLabelTemplate');
    });
    
    
	

	



    // Rota para exibir detalhes de um utente
    Route::get('/utente/{id}', [CheckInOutController::class, 'showUtente'])->name('utente.show');
	

    // Rota para exibir histórico de check-ins e check-outs
    Route::get('/utentes/{id}/historico', [HistoryController::class, 'showCheckoutHistory'])->name('history.show');

    // Rota para exportar histórico como PDF
    Route::get('/utentes/{id}/historico/pdf', [HistoryController::class, 'exportToPDF'])->name('history.export.pdf');

    // Rota para exibir a página de confirmação de check-in/check-out
    Route::get('/confirmar-check/{id}', [CheckInOutController::class, 'showUtente'])->name('confirmar.check');
	
	// Rota POST para check-out direto
Route::post('/utente/{id}/checkout-direct', [CheckInOutController::class, 'checkOutDirect'])->name('utente.checkout.direct');


    // Rota POST para check-in
    Route::post('/utente/{id}/checkin', [CheckInOutController::class, 'checkIn'])->name('utente.checkin');

    // Rota POST para check-out
    Route::post('/utente/{id}/checkout', [CheckInOutController::class, 'checkOut'])->name('utente.checkout');

    // Rota para exibir a página de confirmação (Após check-in ou check-out)
    Route::get('/confirmacao/{id}', function ($id) {
        return view('confirmacao', compact('id')); // Passa o id para a view de confirmação
    })->name('confirmacao');



	/*
    * Companies
    */
    Route::resource('companies', CompaniesController::class, [
        'parameters' => ['company' => 'company_id'],
    ]);

    /*
    * Categories
    */
    Route::resource('categories', CategoriesController::class, [
        'parameters' => ['category' => 'category_id'],
    ]);
  
    /*
    * Labels
    */
    Route::get(
        'labels/{labelName}',
        [LabelsController::class, 'show']
    )->where('labelName', '.*')->name('labels.show');

    /*
     * Locations
     */

    Route::group(['prefix' => 'locations', 'middleware' => ['auth']], function () {

        Route::post(
            'bulkdelete',
            [LocationsController::class, 'postBulkDelete']
        )->name('locations.bulkdelete.show');

        Route::post(
            'bulkedit',
            [LocationsController::class, 'postBulkDeleteStore']
        )->name('locations.bulkdelete.store');


        Route::get('{locationId}/clone',
            [LocationsController::class, 'getClone']
        )->name('clone/location');

        Route::get(
            '{locationId}/printassigned',
            [LocationsController::class, 'print_assigned']
        )->name('locations.print_assigned');

        Route::get(
            '{locationId}/printallassigned',
            [LocationsController::class, 'print_all_assigned']
        )->name('locations.print_all_assigned');

    });

    Route::resource('locations', LocationsController::class, [
        'parameters' => ['location' => 'location_id'],
    ]);


    /*
    * Manufacturers
    */

    Route::group(['prefix' => 'manufacturers', 'middleware' => ['auth']], function () {
        Route::post('{manufacturers_id}/restore', [ManufacturersController::class, 'restore'] )->name('restore/manufacturer');
    });

    Route::resource('manufacturers', ManufacturersController::class, [
        'parameters' => ['manufacturer' => 'manufacturers_id'],
    ]);

    /*
    * Suppliers
    */
    Route::resource('suppliers', SuppliersController::class, [
        'parameters' => ['supplier' => 'supplier_id'],
    ]);

    /*
    * Depreciations
     */
    Route::resource('depreciations', DepreciationsController::class, [
         'parameters' => ['depreciation' => 'depreciation_id'],
     ]);

    /*
    * Status Labels
     */
    Route::resource('statuslabels', StatuslabelsController::class, [
          'parameters' => ['statuslabel' => 'statuslabel_id'],
      ]);

    /*
    * Departments
    */
    Route::resource('departments', DepartmentsController::class, [
        'parameters' => ['department' => 'department_id'],
    ]);
});

/*
|
|--------------------------------------------------------------------------
| Re-Usable Modal Dialog routes.
|--------------------------------------------------------------------------
|
| Routes for various modal dialogs to interstitially create various things
|
*/

Route::group(['middleware' => 'auth', 'prefix' => 'modals'], function () {
    Route::get('{type}/{itemId?}', [ModalController::class, 'show'] )->name('modal.show');
});

/*
|--------------------------------------------------------------------------
| Log Routes
|--------------------------------------------------------------------------
|
| Register all the admin routes.
|
*/

Route::group(['middleware' => 'auth'], function () {
    Route::get(
        'display-sig/{filename}',
        [ActionlogController::class, 'displaySig']
    )->name('log.signature.view');
    Route::get(
        'stored-eula-file/{filename}',
        [ActionlogController::class, 'getStoredEula']
    )->name('log.storedeula.download');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Register all the admin routes.
|
*/

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'authorize:superuser']], function () {
    Route::get('settings', [SettingsController::class, 'getSettings'])->name('settings.general.index');
    Route::post('settings', [SettingsController::class, 'postSettings'])->name('settings.general.save');

    Route::get('branding', [SettingsController::class, 'getBranding'])->name('settings.branding.index');
    Route::post('branding', [SettingsController::class, 'postBranding'])->name('settings.branding.save');

    Route::get('security', [SettingsController::class, 'getSecurity'])->name('settings.security.index');
    Route::post('security', [SettingsController::class, 'postSecurity'])->name('settings.security.save');

    Route::get('groups', [GroupsController::class, 'index'])->name('settings.groups.index');

    Route::get('localization', [SettingsController::class, 'getLocalization'])->name('settings.localization.index');
    Route::post('localization', [SettingsController::class, 'postLocalization'])->name('settings.localization.save');

    Route::get('notifications', [SettingsController::class, 'getAlerts'])->name('settings.alerts.index');
    Route::post('notifications', [SettingsController::class, 'postAlerts'])->name('settings.alerts.save');

    Route::get('slack', [SettingsController::class, 'getSlack'])->name('settings.slack.index');
    Route::post('slack', [SettingsController::class, 'postSlack'])->name('settings.slack.save');

    Route::get('asset_tags', [SettingsController::class, 'getAssetTags'])->name('settings.asset_tags.index');
    Route::post('asset_tags', [SettingsController::class, 'postAssetTags'])->name('settings.asset_tags.save');

    Route::get('barcodes', [SettingsController::class, 'getBarcodes'])->name('settings.barcodes.index');
    Route::post('barcodes', [SettingsController::class, 'postBarcodes'])->name('settings.barcodes.save');

    Route::get('labels', [SettingsController::class, 'getLabels'])->name('settings.labels.index');
    Route::post('labels', [SettingsController::class, 'postLabels'])->name('settings.labels.save');

    Route::get('ldap', [SettingsController::class, 'getLdapSettings'])->name('settings.ldap.index');
    Route::post('ldap', [SettingsController::class, 'postLdapSettings'])->name('settings.ldap.save');

    Route::get('phpinfo', [SettingsController::class, 'getPhpInfo'])->name('settings.phpinfo.index');

    Route::get('oauth', [SettingsController::class, 'api'])->name('settings.oauth.index');

    Route::get('google', [SettingsController::class, 'getGoogleLoginSettings'])->name('settings.google.index');
    Route::post('google', [SettingsController::class, 'postGoogleLoginSettings'])->name('settings.google.save');

    Route::get('purge', [SettingsController::class, 'getPurge'])->name('settings.purge.index');
    Route::post('purge', [SettingsController::class, 'postPurge'])->name('settings.purge.save');

    Route::get('login-attempts', [SettingsController::class, 'getLoginAttempts'])->name('settings.logins.index');

    // Backups
    Route::group(['prefix' => 'backups', 'middleware' => 'auth'], function () {
        Route::get('download/{filename}',
            [SettingsController::class, 'downloadFile'])->name('settings.backups.download');

        Route::delete('delete/{filename}',
            [SettingsController::class, 'deleteFile'])->name('settings.backups.destroy');

        Route::post('/', 
            [SettingsController::class, 'postBackups']
        )->name('settings.backups.create');

        Route::post('/restore/{filename}', 
            [SettingsController::class, 'postRestore']
        )->name('settings.backups.restore');

        Route::post('/upload', 
            [SettingsController::class, 'postUploadBackup']
        )->name('settings.backups.upload');

        // Handle redirect from after POST request from backup restore
        Route::get('/restore/{filename?}', function () {
            return redirect(route('settings.backups.index'));
        });

        Route::get('/', [SettingsController::class, 'getBackups'])->name('settings.backups.index');
    });

    Route::resource('groups', GroupsController::class, [
        'middleware' => ['auth'],
        'parameters' => ['group' => 'group_id'],
    ]);

    Route::get('/', [SettingsController::class, 'index'])->name('settings.index');
});

/*
|--------------------------------------------------------------------------
| Importer Routes
|--------------------------------------------------------------------------
|
|
|
*/

Route::get('/import',
    Importer::class
)->middleware('auth')->name('imports.index');

/*
|--------------------------------------------------------------------------
| Account Routes
|--------------------------------------------------------------------------
|
|
|
*/
Route::group(['prefix' => 'account', 'middleware' => ['auth']], function () {

    // Profile
    Route::get('profile', [ProfileController::class, 'getIndex'])->name('profile');
    Route::post('profile', [ProfileController::class, 'postIndex']);

    Route::get('menu', [ProfileController::class, 'getMenuState'])->name('account.menuprefs');

    Route::get('password', [ProfileController::class, 'password'])->name('account.password.index');
    Route::post('password', [ProfileController::class, 'passwordSave']);

    Route::get('api', [ProfileController::class, 'api'])->name('user.api');

    // View 
    
    Route::get('view-assets', [ViewAssetsController::class, 'getIndex'])->name('view-assets');

    Route::get('requested', [ViewAssetsController::class, 'getRequestedAssets'])->name('account.requested');

    // Profile
    Route::get(
        'requestable-assets',
        [ViewAssetsController::class, 'getRequestableIndex']
    )->name('requestable-assets');
    Route::post(
        'request-asset/{assetId}',
        [ViewAssetsController::class, 'getRequestAsset']
    )->name('account/request-asset');

    Route::post(
        'request/{itemType}/{itemId}/{cancel_by_admin?}/{requestingUser?}',
        [ViewAssetsController::class, 'getRequestItem']
    )->name('account/request-item');

    // Account Dashboard
    Route::get('/', [ViewAssetsController::class, 'getIndex'])->name('account');

    Route::get('accept', [Account\AcceptanceController::class, 'index'])
        ->name('account.accept');

    Route::get('accept/{id}', [Account\AcceptanceController::class, 'create'])
        ->name('account.accept.item');

    Route::post('accept/{id}', [Account\AcceptanceController::class, 'store'])
        ->name('account.store-acceptance');

    Route::get(
        'print',
        [
            ProfileController::class,
            'printInventory'
        ]
    )->name('profile.print');

    Route::post(
        'email',
        [
            ProfileController::class,
            'emailAssetList'
        ]
    )->name('profile.email_assets');

});

Route::group(['middleware' => ['auth']], function () {
    Route::get('reports/audit', 
        [ReportsController::class, 'audit']
    )->name('reports.audit');

    Route::get(
        'reports/depreciation',
        [ReportsController::class, 'getDeprecationReport']
    )->name('reports/depreciation');
    Route::get(
        'reports/export/depreciation',
        [ReportsController::class, 'exportDeprecationReport']
    )->name('reports/export/depreciation');
    Route::get(
        'reports/asset_maintenances',
        [ReportsController::class, 'getAssetMaintenancesReport']
    )->name('reports/asset_maintenances');
    Route::get(
        'reports/export/asset_maintenances',
        [ReportsController::class, 'exportAssetMaintenancesReport']
    )->name('reports/export/asset_maintenances');
    Route::get(
        'reports/licenses',
        [ReportsController::class, 'getLicenseReport']
    )->name('reports/licenses');
    Route::get(
        'reports/export/licenses',
        [ReportsController::class, 'exportLicenseReport']
    )->name('reports/export/licenses');

    Route::get('reports/accessories', [ReportsController::class, 'getAccessoryReport'])->name('reports/accessories');
    Route::get(
        'reports/export/accessories',
        [ReportsController::class, 'exportAccessoryReport']
    )->name('reports/export/accessories');
    Route::get('reports/custom', [ReportsController::class, 'getCustomReport'])->name('reports/custom');
    Route::post('reports/custom', [ReportsController::class, 'postCustom']);

    Route::get(
        'reports/activity',
        [ReportsController::class, 'getActivityReport']
    )->name('reports.activity');

    Route::post('reports/activity', [ReportsController::class, 'postActivityReport']);

    Route::get(
        'reports/unaccepted_assets/{deleted?}',
        [ReportsController::class, 'getAssetAcceptanceReport']
    )->name('reports/unaccepted_assets');
    Route::post(
        'reports/unaccepted_assets/sent_reminder',
        [ReportsController::class, 'sentAssetAcceptanceReminder']
    )->name('reports/unaccepted_assets_sent_reminder');
    Route::delete(
        'reports/unaccepted_assets/{acceptanceId}/delete',
        [ReportsController::class, 'deleteAssetAcceptance']
    )->name('reports/unaccepted_assets_delete');
    Route::post(
        'reports/unaccepted_assets/{deleted?}',
        [ReportsController::class, 'postAssetAcceptanceReport']
    )->name('reports/export/unaccepted_assets');
});

Route::get(
    'auth/signin',
    [LoginController::class, 'legacyAuthRedirect']
);


/*
|--------------------------------------------------------------------------
| Setup Routes
|--------------------------------------------------------------------------
|
|
|
*/
Route::group(['prefix' => 'setup', 'middleware' => 'web'], function () {
    Route::get(
        'user',
        [SettingsController::class, 'getSetupUser']
    )->name('setup.user');

    Route::post(
        'user',
        [SettingsController::class, 'postSaveFirstAdmin']
    )->name('setup.user.save');


    Route::get(
        'migrate',
        [SettingsController::class, 'getSetupMigrate']
    )->name('setup.migrate');

    Route::get(
        'done',
        [SettingsController::class, 'getSetupDone']
    )->name('setup.done');

    Route::get(
        'mailtest',
        [SettingsController::class, 'ajaxTestEmail']
    )->name('setup.mailtest');

    Route::get(
        '/',
        [SettingsController::class, 'getSetupIndex']
    )->name('setup');
});





Route::group(['middleware' => 'web'], function () {

    Route::get(
        'login',
        [LoginController::class, 'showLoginForm']
    )->name("login");

    Route::post(
        'login',
        [LoginController::class, 'login']
    );

    Route::get(
        'two-factor-enroll',
        [LoginController::class, 'getTwoFactorEnroll']
    )->name('two-factor-enroll');

    Route::get(
        'two-factor',
        [LoginController::class, 'getTwoFactorAuth']
    )->name('two-factor');

    Route::post(
        'two-factor',
        [LoginController::class, 'postTwoFactorAuth']
    );

    Route::post(
        'password/email',
        [ForgotPasswordController::class, 'sendResetLinkEmail']
    )->name('password.email')->middleware('throttle:forgotten_password');

    Route::get(
        'password/reset',
        [ForgotPasswordController::class, 'showLinkRequestForm']
    )->name('password.request')->middleware('throttle:forgotten_password');


    Route::post(
        'password/reset',
        [ResetPasswordController::class, 'reset']
    )->name('password.update')->middleware('throttle:forgotten_password');

    Route::get(
        'password/reset/{token}',
        [ResetPasswordController::class, 'showResetForm']
    )->name('password.reset');


    Route::post(
        'password/email',
        [ForgotPasswordController::class, 'sendResetLinkEmail']
    )->name('password.email')->middleware('throttle:forgotten_password');


     // Socialite Google login
    Route::get('google', 'App\Http\Controllers\GoogleAuthController@redirectToGoogle')->name('google.redirect');
    Route::get('google/callback', 'App\Http\Controllers\GoogleAuthController@handleGoogleCallback')->name('google.callback');


    Route::get(
        '/',
        [
            'as' => 'home',
            'middleware' => ['auth'],
            'uses' => 'DashboardController@getIndex' ]
    );

    // need to keep GET /logout for SAML SLO
    Route::get(
        'logout',
        [LoginController::class, 'logout']
    )->name('logout.get');

    Route::post(
        'logout',
        [LoginController::class, 'logout']
    )->name('logout.post');
});

//Auth::routes();

Route::get(
    '/health', 
    [HealthController::class, 'get']
)->name('health');

Route::middleware(['auth'])->get(
    '/',
    [DashboardController::class, 'index']
)->name('home');
