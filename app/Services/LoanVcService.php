<?php

namespace App\Services;

use App\Enums\Statuses\LoanVcStatus;
use App\Enums\Statuses\OpportunityStatus;
use App\Enums\Types\LogTrailActionType;
use App\Enums\Types\LogTrailEntityType;
use App\Exceptions\WarningException;
use App\Helpers\Http\Uploader\Uploader;
use App\Models\LoanVc;
use App\Models\Opportunity;
use App\Repositories\BaseRepository;
use App\Repositories\LoanVcRepository;
use App\Services\Traits\EntityDeletionTrait;
use Illuminate\Database\Eloquent\Model;

class LoanVcService extends BasePersistableService
{
    use EntityDeletionTrait;

    protected LogTrailEntityType $logTrailPawnType = LogTrailEntityType::LOAN_VC;

    public function __construct(
        private readonly LoanVcRepository     $repository,
        private readonly LogTrailService      $logTrailService,
        private readonly LoanVcCountryService $loanVcCountryService,
    )
    {
    }

    public function create(
        int    $createdBy,
        array  $countryIds,
        string $businessTypeId,
        string $opportunityTypeId,
        string $organisation,
        float  $lowerAmount,
        float  $upperAmount,
        string $description,
        string $applicationUrl,
        string $closingAt,
    ): Opportunity|Model
    {
        $logo = match (defined('IS_MIGRATING')) {
            true => '/logo.png',
            false => Uploader::image(fieldName: 'logo')[0]->getRelativePath(),
        };

        $loanVc = $this->repository->create(
            createdBy: $createdBy,
            businessTypeId: $businessTypeId,
            opportunityTypeId: $opportunityTypeId,
            organisation: $organisation,
            lowerAmount: $lowerAmount,
            upperAmount: $upperAmount,
            logo: $logo,
            description: $description,
            applicationUrl: $applicationUrl,
            closingAt: $closingAt,
        );

        foreach ($countryIds as $countryId) {
            $this->loanVcCountryService->create(
                createdBy: $createdBy,
                loanVcId: $loanVc['id'],
                countryId: $countryId,
            );
        }

        return $loanVc;
    }

    public function update(
        int    $id,
        int    $updatedBy,
        string $businessTypeId,
        string $opportunityTypeId,
        string $organisation,
        float  $lowerAmount,
        float  $upperAmount,
        string $description,
        string $applicationUrl,
        string $closingAt,
    ): Model|Opportunity
    {
        $lvc = $this->repository->findRequiredById($id);

        $logo = match (request()->hasFile('logo')) {
            false => $lvc['logo'],
            true => Uploader::image(fieldName: 'logo')[0]->getRelativePath(),
        };

        $lvc = $this->repository->update(
            lvc: $lvc,
            businessTypeId: $businessTypeId,
            opportunityTypeId: $opportunityTypeId,
            organisation: $organisation,
            lowerAmount: $lowerAmount,
            upperAmount: $upperAmount,
            logo: $logo,
            description: $description,
            applicationUrl: $applicationUrl,
            closingAt: $closingAt,
        );

        $this->logTrailService->create(
            userId: $updatedBy,
            entityId: $id,
            entityType: $this->logTrailPawnType,
            action: LogTrailActionType::UPDATE,
            desc: 'Opportunity updated',
            data: $lvc,
        );

        return $lvc;
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
            'all' => LoanVc::query()->count(),
            'ongoing' => LoanVc::query()
                ->where('status', LoanVcStatus::ONGOING->lowercase())
                ->count(),
            'closed' => LoanVc::query()
                ->where('status', LoanVcStatus::CLOSED->lowercase())
                ->count(),
        ];
    }

    public function repository(): BaseRepository
    {
        return $this->repository;
    }
}