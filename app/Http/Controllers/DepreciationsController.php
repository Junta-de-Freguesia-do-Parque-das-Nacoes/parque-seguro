<?php

namespace App\Http\Controllers;

use App\Models\Depreciation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use \Illuminate\Contracts\View\View;
/**
 * This controller handles all actions related to Depreciations for
 * the Parque Seguro Asset Management application.
 *
 * @version    v1.0
 */
class DepreciationsController extends Controller
{
    /**
     * Returns a view that invokes the ajax tables which actually contains
     * the content for the depreciation listing, which is generated in getDatatable.
     *
     * @author [A. Gianotto] [<snipe@snipe.net]
     * @see DepreciationsController::getDatatable() method that generates the JSON response
     * @since [v1.0]
     */
    public function index() : View
    {
        $this->authorize('view', Depreciation::class);
        return view('depreciations/index');
    }

    /**
     * Returns a view that displays a form to create a new depreciation.
     *
     * @author [A. Gianotto] [<snipe@snipe.net]
     * @see DepreciationsController::postCreate()
     * @since [v1.0]
     */
    public function create() : View
    {
        $this->authorize('create', Depreciation::class);

        // Show the page
        return view('depreciations/edit')->with('item', new Depreciation);
    }

    /**
     * Validates and stores the new depreciation data.
     *
     * @author [A. Gianotto] [<snipe@snipe.net]
     * @see DepreciationsController::postCreate()
     * @since [v1.0]
     * @param Request $request
     */
    public function store(Request $request) : RedirectResponse
    {
        $this->authorize('create', Depreciation::class);

        // create a new instance
        $depreciation = new Depreciation();
        // Depreciation data
        $depreciation->name = $request->input('name');
        $depreciation->months = $request->input('months');
        $depreciation->user_id = Auth::id();
        $depreciation->depreciation_min = $request->input('depreciation_min');

        // Was the asset created?
        if ($depreciation->save()) {
            // Redirect to the new depreciation  page
            return redirect()->route('depreciations.index')->with('success', trans('admin/depreciations/message.create.success'));
        }

        return redirect()->back()->withInput()->withErrors($depreciation->getErrors());
    }

    /**
     * Returns a view that displays a form to update a depreciation.
     *
     * @author [A. Gianotto] [<snipe@snipe.net]
     * @see DepreciationsController::postEdit()
     * @param int $depreciationId
     * @since [v1.0]
     */
    public function edit($depreciationId = null) : RedirectResponse | View
    {
        // Check if the depreciation exists
        if (is_null($item = Depreciation::find($depreciationId))) {
            // Redirect to the blogs management page
            return redirect()->route('depreciations.index')->with('error', trans('admin/depreciations/message.does_not_exist'));
        }

        $this->authorize('update', $item);

        return view('depreciations/edit', compact('item'));
    }

    /**
     * Validates and stores the updated depreciation data.
     *
     * @author [A. Gianotto] [<snipe@snipe.net]
     * @see DepreciationsController::getEdit()
     * @param Request $request
     * @param int $depreciationId
     * @since [v1.0]
     */
    public function update(Request $request, $depreciationId = null) : RedirectResponse
    {
        // Check if the depreciation exists
        if (is_null($depreciation = Depreciation::find($depreciationId))) {
            // Redirect to the blogs management page
            return redirect()->route('depreciations.index')->with('error', trans('admin/depreciations/message.does_not_exist'));
        }

        $this->authorize('update', $depreciation);

        // Depreciation data
        $depreciation->name             = $request->input('name');
        $depreciation->months           = $request->input('months');
        $depreciation->depreciation_min = $request->input('depreciation_min');

        // Was the asset created?
        if ($depreciation->save()) {
            // Redirect to the depreciation page
            return redirect()->route('depreciations.index')->with('success', trans('admin/depreciations/message.update.success'));
        }

        return redirect()->back()->withInput()->withErrors($depreciation->getErrors());
    }

    /**
     * Validates and deletes a selected depreciation.
     *
     * This is a hard-delete. We do not currently soft-delete depreciations.
     *
     * @author [A. Gianotto] [<snipe@snipe.net]
     * @since [v1.0]
     * @param int $depreciationId
     */
    public function destroy($depreciationId) : RedirectResponse
    {
        // Check if the depreciation exists
        if (is_null($depreciation = Depreciation::withCount('models as models_count')->find($depreciationId))) {
            return redirect()->route('depreciations.index')->with('error', trans('admin/depreciations/message.not_found'));
        }

        $this->authorize('delete', $depreciation);

        if ($depreciation->models_count > 0) {
            // Redirect to the asset management page
            return redirect()->route('depreciations.index')->with('error', trans('admin/depreciations/message.assoc_users'));
        }

        $depreciation->delete();
        // Redirect to the depreciations management page
        return redirect()->route('depreciations.index')->with('success', trans('admin/depreciations/message.delete.success'));
    }

    /**
     * Returns a view that displays a form to display depreciation listing
     *
     * @author [A. Gianotto] [<snipe@snipe.net]
     * @see DepreciationsController::postEdit()
     * @param int $depreciationId
     * @since [v1.0]
     */
    public function show($id) : View | RedirectResponse
    {
        if (is_null($depreciation = Depreciation::find($id))) {
            // Redirect to the blogs management page
            return redirect()->route('depreciations.index')->with('error', trans('admin/depreciations/message.does_not_exist'));
        }

        $this->authorize('view', $depreciation);

        return view('depreciations/view', compact('depreciation'));
    }
}
