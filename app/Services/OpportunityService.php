<?php

namespace App\Services;

use App\Enums\Statuses\OpportunityStatus;
use App\Enums\Types\LogTrailActionType;
use App\Enums\Types\LogTrailEntityType;
use App\Exceptions\WarningException;
use App\Helpers\Http\Uploader\Uploader;
use App\Models\Opportunity;
use App\Repositories\BaseRepository;
use App\Repositories\OpportunityRepository;
use App\Services\Traits\EntityDeletionTrait;
use Illuminate\Database\Eloquent\Model;

class OpportunityService extends BasePersistableService
{
    use EntityDeletionTrait;

    protected LogTrailEntityType $logTrailPawnType = LogTrailEntityType::OPPORTUNITY;

    public function __construct(
        private readonly OpportunityRepository $repository,
        private readonly LogTrailService       $logTrailService,
    )
    {
    }

    public function create(
        int    $createdBy,
        string $countryId,
        string $industryId,
        string $opportunityTypeId,
        string $name,
        string $organisation,
        float  $lowerAmount,
        float  $upperAmount,
        string $overview,
        string $applicationUrl,
        string $closingAt,
    ): Opportunity|Model
    {
        $logo = match (defined('IS_MIGRATING')) {
            true => '/images/spiralover.png',
            false => Uploader::image(fieldName: 'logo')[0]->getRelativePath(),
        };

        return $this->repository->create(
            createdBy: $createdBy,
            countryId: $countryId,
            industryId: $industryId,
            opportunityTypeId: $opportunityTypeId,
            name: $name,
            organisation: $organisation,
            lowerAmount: $lowerAmount,
            upperAmount: $upperAmount,
            logo: $logo,
            overview: $overview,
            applicationUrl: $applicationUrl,
            closingAt: $closingAt,
        );
    }

    public function update(
        int    $id,
        int    $updatedBy,
        string $countryId,
        string $businessTypeId,
        string $opportunityTypeId,
        string $name,
        string $organisation,
        float  $lowerAmount,
        float  $upperAmount,
        string $overview,
        string $applicationUrl,
        string $closingAt,
    ): Model|Opportunity
    {
        $opportunity = $this->repository->findRequiredById($id);

        $logo = match (request()->hasFile('logo')) {
            false => $opportunity['logo'],
            true => Uploader::image(fieldName: 'logo')[0]->getRelativePath(),
        };

        $opportunity = $this->repository->update(
            opportunity: $opportunity,
            countryId: $countryId,
            businessTypeId: $businessTypeId,
            opportunityTypeId: $opportunityTypeId,
            name: $name,
            organisation: $organisation,
            lowerAmount: $lowerAmount,
            upperAmount: $upperAmount,
            logo: $logo,
            overview: $overview,
            applicationUrl: $applicationUrl,
            closingAt: $closingAt,
        );

        $this->logTrailService->create(
            userId: $updatedBy,
            entityId: $id,
            entityType: $this->logTrailPawnType,
            action: LogTrailActionType::UPDATE,
            desc: 'Opportunity updated',
            data: $opportunity,
        );

        return $opportunity;
    }

    /**
     * @throws WarningException
     */
    public function changeLogo(string $id, int $changedBy): Model|Opportunity
    {
        $opp = $this->repository->findRequiredById($id);
        $logo = Uploader::image(fieldName: 'image')[0]->getRelativePath();
        $opp->update(compact('logo'));

        $this->logTrailService->create(
            userId: $changedBy,
            entityId: $id,
            entityType: $this->logTrailPawnType,
            action: LogTrailActionType::UPDATE,
            desc: 'Opportunity logo changed',
            data: $opp,
        );

        return $opp;
    }

    public function close(string $id, int $closedBy): Model|Opportunity
    {
        $opp = $this->repository->findRequiredById($id);
        $opp->update(['status' => OpportunityStatus::CLOSED->lowercase()]);

        $this->logTrailService->create(
            userId: $closedBy,
            entityId: $id,
            entityType: $this->logTrailPawnType,
            action: LogTrailActionType::UPDATE,
            desc: 'Opportunity closed',
            data: $opp,
        );

        return $opp;
    }

    public function pageMetrics(): array
    {
        return [
            'all' => Opportunity::query()->count(),
            'ongoing' => Opportunity::query()
                ->where('status', OpportunityStatus::ONGOING->lowercase())
                ->count(),
            'closed' => Opportunity::query()
                ->where('status', OpportunityStatus::CLOSED->lowercase())
                ->count(),
        ];
    }

    public function repository(): BaseRepository
    {
        return $this->repository;
    }
}
