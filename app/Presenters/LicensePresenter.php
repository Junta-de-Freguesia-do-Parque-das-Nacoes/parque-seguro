<?php

namespace App\Presenters;

/**
 * Class LicensePresenter
 */
class LicensePresenter extends Presenter
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
                'field' => 'company',
                'searchable' => true,
                'sortable' => true,
                'switchable' => true,
                'title' => trans('admin/companies/table.title'),
                'visible' => false,
                'formatter' => 'companiesLinkObjFormatter',
            ], [
                'field' => 'name',
                'searchable' => true,
                'sortable' => true,
                'switchable' => false,
                'title' => trans('general.name'),
                'formatter' => 'licensesLinkFormatter',
            ],  [
                'field' => 'expiration_date',
                'searchable' => true,
                'sortable' => true,
                'title' => trans('admin/licenses/form.expiration'),
                'formatter' => 'dateDisplayFormatter',
            ], [
                'field' => 'category',
                'searchable' => true,
                'sortable' => true,
                'switchable' => true,
                'title' => trans('general.category'),
                'visible' => true,
                'formatter' => 'categoriesLinkObjFormatter',
            ],  [
                'field' => 'seats',
                'searchable' => false,
                'sortable' => true,
                'title' => trans('admin/accessories/general.total'),
				'visible' => true,
            ], [
                'field' => 'free_seats_count',
                'searchable' => false,
                'sortable' => true,
                'title' => trans('admin/accessories/general.remaining'),
				'visible' => true,
            ],
            [
                'field' => 'purchase_date',
                'searchable' => true,
                'sortable' => true,
                'visible' => true,
                'title' => trans('general.purchase_date'),
                'formatter' => 'dateDisplayFormatter',
            ],
            [
                'field' => 'termination_date',
                'searchable' => true,
                'sortable' => true,
                'visible' => true,
                'title' => trans('admin/licenses/form.termination_date'),
                'formatter' => 'dateDisplayFormatter',
            ],
             [
                'field' => 'reassignable',
                'searchable' => false,
                'sortable' => true,
                'visible' => false,
                'title' => trans('admin/licenses/form.reassignable'),
                'formatter' => 'trueFalseFormatter',
            ],
            [
                'field' => 'purchase_cost',
                'searchable' => true,
                'sortable' => true,
                'visible' => true,
                'title' => trans('general.purchase_cost'),
                'footerFormatter' => 'sumFormatterQuantity',
                'class' => 'text-right',
            ], [
                'field' => 'purchase_order',
                'searchable' => true,
                'sortable' => true,
                'visible' => true,
                'title' => trans('admin/licenses/form.purchase_order'),
            ], [
                'field' => 'order_number',
                'searchable' => true,
                'sortable' => true,
                'visible' => false,
                'title' => trans('general.order_number'),
            ], [
                'field' => 'created_by',
                'searchable' => false,
                'sortable' => true,
                'title' => trans('general.admin'),
                'visible' => false,
                'formatter' => 'usersLinkObjFormatter',
            ], [
                'field' => 'created_at',
                'searchable' => false,
                'sortable' => true,
                'visible' => true,
                'title' => trans('general.created_at'),
                'formatter' => 'dateDisplayFormatter',
            ], [
                'field' => 'updated_at',
                'searchable' => false,
                'sortable' => true,
                'visible' => false,
                'title' => trans('general.updated_at'),
                'formatter' => 'dateDisplayFormatter',
            ],
            [
                'field' => 'notes',
                'searchable' => true,
                'sortable' => true,
                'visible' => false,
                'title' => trans('general.notes'),
                'formatter' => 'notesFormatter',
            ],
        ];

        $layout[] = [
            'field' => 'checkincheckout',
            'searchable' => false,
            'sortable' => false,
            'switchable' => false,
            'title' => trans('Alocar devolver vaga'),
            'visible' => true,
            'formatter' => 'licensesInOutFormatter',
        ];

        $layout[] = [
            'field' => 'actions',
            'searchable' => false,
            'sortable' => false,
            'switchable' => false,
            'title' => trans('table.actions'),
            'formatter' => 'licensesActionsFormatter',
        ];

        return json_encode($layout);
    }

    /**
     * Json Column Layout for bootstrap table
     * @return string
     */
    public static function dataTableLayoutSeats()
    {
        $layout = [
            [
                'field' => 'id',
                'searchable' => false,
                'sortable' => true,
                'switchable' => true,
                'title' => trans('general.id'),
                'visible' => false,
           ],
           [
                'field' => 'name',
                'searchable' => false,
                'sortable' => false,
                'sorter'   => 'numericOnly',
                'switchable' => true,
                'title' => trans('admin/licenses/general.seat'),
                'visible' => true,
            ], [
                'field' => 'assigned_user',
                'searchable' => false,
                'sortable' => false,
                'switchable' => true,
                'title' => trans('admin/licenses/general.user'),
                'visible' => false,
                'formatter' => 'usersLinkObjFormatter',
            ], [
                'field' => 'assigned_user.email',
                'searchable' => false,
                'sortable' => false,
                'switchable' => true,
                'title' => trans('admin/users/table.email'),
                'visible' => false,
                'formatter' => 'emailFormatter',
            ], [
                'field' => 'department',
                'searchable' => false,
                'sortable' => true,
                'switchable' => true,
                'title' => trans('general.department'),
                'visible' => false,
                'formatter' => 'departmentNameLinkFormatter',
            ], [
                'field' => 'assigned_asset',
                'searchable' => false,
                'sortable' => false,
                'switchable' => true,
                'title' => trans('admin/licenses/form.asset'),
                'visible' => true,
                'formatter' => 'hardwareLinkObjFormatter',
            ], [
                'field' => 'location',
                'searchable' => false,
                'sortable' => false,
                'switchable' => true,
                'title' => trans('general.location'),
                'visible' => false,
                'formatter' => 'locationsLinkObjFormatter',
            ],
            [
                'field' => 'notes',
                'searchable' => false,
                'sortable' => false,
                'visible' => true,
                'title' => trans('general.notes'),
                'formatter' => 'notesFormatter'
            ],
            [
                'field' => 'checkincheckout',
                'searchable' => false,
                'sortable' => false,
                'switchable' => false,
                'title' => trans('Alocar devolver vaga'),
                'visible' => true,
                'formatter' => 'licenseSeatInOutFormatter',
            ],
        ];

        return json_encode($layout);
    }

    /**
     * Link to this licenses Name
     * @return string
     */
    public function nameUrl()
    {
        return (string) link_to_route('licenses.show', $this->name, $this->id);
    }

    /**
     * Link to this licenses Name
     * @return string
     */
    public function fullName()
    {
        return $this->name;
    }

    /**
     * Link to this licenses serial
     * @return string
     */
    public function serialUrl()
    {
        return (string) link_to('/licenses/'.$this->id, mb_strimwidth($this->serial, 0, 50, '...'));
    }

    /**
     * Url to view this item.
     * @return string
     */
    public function viewUrl()
    {
        return route('licenses.show', $this->id);
    }
}
