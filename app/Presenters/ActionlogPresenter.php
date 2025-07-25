<?php

namespace App\Presenters;

/**
 * Class CompanyPresenter
 */
class ActionlogPresenter extends Presenter
{
    public function admin()
    {
        if ($user = $this->model->user) {
            if (empty($user->deleted_at)) {
                return $user->present()->nameUrl();
            }
            // The user was deleted
            return '<del>'.$user->getFullNameAttribute().'</del> (deleted)';
        }

        return '';
    }

    public function actionSourceLabel()
    {
        return match ($this->model->action_source) {
            'ee-gui', 'portal-ee' => '<span class="badge bg-primary"><i class="fas fa-user-shield me-1"></i> Portal EE</span>',
            'gui'                 => '<span class="badge bg-secondary"><i class="fas fa-desktop me-1"></i> Backoffice</span>',
            'api'                 => '<span class="badge bg-info"><i class="fas fa-code me-1"></i> API</span>',
            'qr-public'           => '<span class="badge bg-warning text-dark"><i class="fas fa-qrcode me-1"></i>Leitura QR Code</span>',
            'qr-ee'               => '<span class="badge bg-success"><i class="fas fa-qrcode me-1"></i> QR Portal EE</span>',
            'cli/unknown'         => '<span class="badge bg-dark"><i class="fas fa-terminal me-1"></i> CLI</span>',
            default               => '<span class="badge bg-light text-muted">—</span>',
        };
    }
    
    
    

    public function item()
    {
        if ($this->action_type == 'uploaded') {
            return (string) link_to_route('show/userfile', $this->model->filename, [$this->model->item->id, $this->model->id]);
        }
        if ($item = $this->model->item) {
            if (empty($item->deleted_at)) {
                return $this->model->item->present()->nameUrl();
            }
            // The item was deleted
            return '<del>'.$item->name.'</del> (deleted)';
        }

        return '';
    }

    public function icon()
    {

        // User related icons
        if ($this->itemType() == 'user') {

            if ($this->actionType()=='2fa reset') {
                return 'fa-solid fa-mobile-screen';
            }

            if ($this->actionType()=='create new') {
                return 'fa-solid fa-user-plus';
            }

            if ($this->actionType()=='merged') {
                return 'fa-solid fa-people-arrows';
            }

            if ($this->actionType()=='delete') {
                return 'fa-solid fa-user-minus';
            }

            if ($this->actionType()=='delete') {
                return 'fa-solid fa-user-minus';
            }

            if ($this->actionType()=='update') {
                return 'fa-solid fa-user-pen';
            }

             return 'fa-solid fa-user';
        }

        // Everything else
        if ($this->actionType()=='create new') {
            return 'fa-solid fa-plus';
        }

        if ($this->actionType()=='delete') {
            return 'fa-solid fa-trash';
        }

        if ($this->actionType()=='update') {
            return 'fa-solid fa-pen';
        }

        if ($this->actionType()=='restore') {
            return 'fa-solid fa-trash-arrow-up';
        }

        if ($this->actionType()=='upload') {
            return 'fas fa-paperclip';
        }

        if ($this->actionType()=='checkout') {
            return 'fa-solid fa-rotate-left';
        }

        if ($this->actionType()=='checkin from') {
            return 'fa-solid fa-rotate-right';
        }

        return 'fa-solid fa-rotate-right';

    }

    public function actionType()
    {
        return match ($this->model->action_type) {
            'checkin'         => 'Entrada',
            'checkout'        => 'Saída',
            'entrada_qrcode'  => 'Entrada (QR Code)',
            'saida_qrcode'    => 'Saída (QR Code)',
            'uploaded'        => 'Ficheiro Carregado',
            'update'          => 'Atualização',
            'delete'          => 'Remoção',
            'create new'      => 'Criação',
            default           => ucfirst($this->model->action_type),
        };
    }
    

    public function target()
    {
        $target = null;
        // Target is messy.
        // On an upload, the target is the item we are uploading to, stored as the "item" in the log.
        if ($this->action_type == 'uploaded') {
            $target = $this->model->item;
        } elseif (($this->action_type == 'accepted') || ($this->action_type == 'declined')) {
            // If we are logging an accept/reject, the target is not stored directly,
            // so we access it through who the item is assigned to.
            // FIXME: On a reject it's not assigned to anyone.
            $target = $this->model->item->assignedTo;
        } elseif ($this->action_type == 'requested') {
            if ($this->model->user) {
                $target = $this->model->user;
            }
        } elseif ($this->model->target) {
            // Otherwise, we'll just take the target of the log.
            $target = $this->model->target;
        }

        if ($target) {
            if (empty($target->deleted_at)) {
                return $target->present()->nameUrl();
            }

            return '<del>'.$target->present()->name().'</del>';
        }

        return '';
    }
}
