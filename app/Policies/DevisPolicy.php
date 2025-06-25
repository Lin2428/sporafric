<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Devis;
use Illuminate\Auth\Access\HandlesAuthorization;

class DevisPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_devis');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Devis $devis): bool
    {
        return $user->can('view_devis');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_devis');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Devis $devis): bool
    {
        return $user->can('update_devis');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Devis $devis): bool
    {
        return $user->can('delete_devis');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_devis');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Devis $devis): bool
    {
        return $user->can('force_delete_devis');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_devis');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Devis $devis): bool
    {
        return $user->can('restore_devis');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_devis');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Devis $devis): bool
    {
        return $user->can('replicate_devis');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_devis');
    }
}
