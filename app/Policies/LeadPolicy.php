<?php

namespace Codevery\Policies;

use Codevery\Lead;
use Codevery\Manager;
use Illuminate\Auth\Access\HandlesAuthorization;

class LeadPolicy
{
    use HandlesAuthorization;

    public function view(Manager $manager, Lead $lead)
    {
        return $lead->manager->chief_id === $manager->id || $manager->id === $lead->manager_id;
    }

    public function update(Manager $manager, Lead $lead)
    {
        return $manager->id === $lead->manager_id;
    }

    public function reassign(Manager $manager, Lead $lead)
    {
        return $lead->manager->chief_id === $manager->id;
    }

    public function highLevel(Manager $manager)
    {
        return $manager->isHighLevelAccess();
    }

    public function destroy(Manager $manager, Lead $lead)
    {
        return $manager->isAdmin();
    }

}
