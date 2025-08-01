<?php

namespace App\Presenters;

/**
 * Class CompanyPresenter
 */
class CompanyPresenter extends Presenter
{
    /**
     * Json Column Layout for bootstrap table
     * @return string
     */
    public static function dataTableLayout()
    {
        $layout = [
            [
                'field' => 'id',
                'searchable' => false,
                'sortable' => true,
                'switchable' => true,
                'title' => trans('general.id'),
                'visible' => false,
            ], [
                'field' => 'name',
                'searchable' => true,
                'sortable' => true,
                'switchable' => false,
                'title' => trans('admin/companies/table.name'),
                'visible' => true,
                'formatter' => 'companiesLinkFormatter',
            ], [
                'field' => 'phone',
                'searchable' => true,
                'sortable' => true,
                'switchable' => true,
                'title' => trans('admin/users/table.phone'),
                'visible' => false,
                'formatter'    => 'phoneFormatter',
            ], [
                'field' => 'fax',
                'searchable' => true,
                'sortable' => true,
                'switchable' => true,
                'title' => trans('admin/suppliers/table.fax'),
                'visible' => false,
                'formatter'    => 'phoneFormatter',
            ], [
                'field' => 'email',
                'searchable' => true,
                'sortable' => true,
                'switchable' => true,
                'title' => trans('admin/suppliers/table.email'),
                'visible' => true,
				'formatter' => 'emailFormatter',
            ], [
                'field' => 'image',
                'searchable' => false,
                'sortable' => true,
                'switchable' => true,
                'title' => trans('general.image'),
                'visible' => true,
                'formatter' => 'imageFormatter',
            ], [
                'field' => 'users_count',
                'searchable' => false,
                'sortable' => true,
                'title' => '<span class="hidden-xs"><i class="fas fa-users"></i></span><span class="hidden-md hidden-lg">'.trans('general.users').'</span></th>',
                'visible' => true,

            ], [
                'field' => 'assets_count',
                'searchable' => false,
                'sortable' => true,
                'title' => '<span class="hidden-xs"><i class="fas fa-children" aria-hidden="true"></i></span><span class="hidden-md hidden-lg">'.trans('general.assets').'</span>',
                'visible' => true,

            ], [
                'field' => 'licenses_count',
                'searchable' => false,
                'sortable' => true,
                'visible' => true,
                'title' => ' <span class="hidden-xs"><i class="fas fa-scroll"></i></span><span class="hidden-md hidden-lg">'.trans('general.licenses').'</span>',
            ], [
                'field' => 'accessories_count',
                'searchable' => false,
                'sortable' => true,
                'visible' => true,
                'title' => ' <span class="hidden-xs"><i class="fas fa-puzzle-piece"></i></span><span class="hidden-md hidden-lg">'.trans('general.accessories').'</span>',
            ], [
                'field' => 'consumables_count',
                'searchable' => false,
                'sortable' => true,
                'visible' => true,
                'title' => ' <span class="hidden-xs"><i class="fas fa-box"></i></span><span class="hidden-md hidden-lg">'.trans('general.consumables').'</span>',
            ], [
                'field' => 'components_count',
                'searchable' => false,
                'sortable' => true,
                'visible' => true,
                'title' => ' <span class="hidden-xs"><i class="fas fa-tools"></i></span><span class="hidden-md hidden-lg">'.trans('general.components').'</span>',
            ], [
                'field' => 'updated_at',
                'searchable' => false,
                'sortable' => true,
                'visible' => false,
                'title' => trans('general.updated_at'),
                'formatter' => 'createdAtFormatter',
            ], [
                'field' => 'created_at',
                'searchable' => false,
                'sortable' => true,
                'visible' => false,
                'title' => trans('general.created_at'),
                'formatter' => 'createdAtFormatter',
            ], [
                'field' => 'actions',
                'searchable' => false,
                'sortable' => false,
                'switchable' => false,
                'title' => trans('table.actions'),
                'visible' => true,
                'formatter' => 'companiesActionsFormatter',
            ],
        ];

        return json_encode($layout);
    }

    /**
     * Link to this companies name
     * @return string
     */
    public function nameUrl()
    {
        return (string) link_to_route('companies.show', $this->name, $this->id);
    }

    /**
     * Url to view this item.
     * @return string
     */
    public function viewUrl()
    {
        return route('companies.show', $this->id);
    }
}
