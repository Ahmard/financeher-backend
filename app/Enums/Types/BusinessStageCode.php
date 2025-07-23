<?php

namespace App\Enums\Types;

use App\Enums\EnumHelper\DBCompatibleEnumInterface;
use App\Enums\EnumHelper\DBCompatibleEnumTrait;

enum BusinessStageCode: string implements DBCompatibleEnumInterface
{
    use DBCompatibleEnumTrait;

    case IDEA_CONCEPT = 'FNC/BUSINESS-STG/IDEA-CONCEPT';
    case STARTUP = 'FNC/BUSINESS-STG/STARTUP';
    case GROWTH = 'FNC/BUSINESS-STG/GROWTH';
    case ESTABLISHED = 'FNC/BUSINESS-STG/ESTABLISHED';

    public function getDisplayName(): string
    {
        return match ($this) {
            self::IDEA_CONCEPT => 'Idea/Concept',
            self::STARTUP => 'Startup (0-2 years)',
            self::GROWTH => 'Growth (2-5 years)',
            self::ESTABLISHED => 'Established (5+ years)',
        };
    }

    public function getShortName(): string
    {
        return match ($this) {
            self::IDEA_CONCEPT => 'Idea/Concept',
            self::STARTUP => 'Startup',
            self::GROWTH => 'Growth',
            self::ESTABLISHED => 'Established',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::IDEA_CONCEPT => 'Early conceptual stage where the business idea is being developed, validated, and refined. This includes market research, feasibility studies, business plan development, and initial prototype creation.',
            self::STARTUP => 'Early operational stage for businesses that have recently launched and are focused on establishing market presence, building initial customer base, refining product-market fit, and securing initial funding.',
            self::GROWTH => 'Expansion stage where businesses experience rapid growth in revenue, customer base, and market share. Focus shifts to scaling operations, expanding team, optimizing processes, and potentially seeking additional investment.',
            self::ESTABLISHED => 'Mature stage for well-established businesses with proven track records, stable revenue streams, established market position, and focus on sustainability, innovation, diversification, and long-term strategic planning.',
        };
    }

    public function getAgeRange(): string
    {
        return match ($this) {
            self::IDEA_CONCEPT => 'Pre-launch',
            self::STARTUP => '0-2 years',
            self::GROWTH => '2-5 years',
            self::ESTABLISHED => '5+ years',
        };
    }

    public function getCharacteristics(): array
    {
        return match ($this) {
            self::IDEA_CONCEPT => [
                'Market research and validation',
                'Business plan development',
                'Prototype creation',
                'Seeking initial funding',
                'Team formation',
            ],
            self::STARTUP => [
                'Product launch',
                'Initial customer acquisition',
                'Revenue generation',
                'Team building',
                'Market validation',
            ],
            self::GROWTH => [
                'Rapid revenue growth',
                'Market expansion',
                'Scaling operations',
                'Process optimization',
                'Additional funding rounds',
            ],
            self::ESTABLISHED => [
                'Stable market position',
                'Consistent profitability',
                'Mature processes',
                'Strategic planning',
                'Innovation and diversification',
            ],
        };
    }

    public static function getAllCodes(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }

    public static function getAllDisplayNames(): array
    {
        return array_map(fn ($case) => $case->getDisplayName(), self::cases());
    }

    public static function fromCode(string $code): ?self
    {
        return self::tryFrom($code);
    }

    public static function exists(string $code): bool
    {
        return self::tryFrom($code) !== null;
    }

    public function getNextStage(): ?self
    {
        return match ($this) {
            self::IDEA_CONCEPT => self::STARTUP,
            self::STARTUP => self::GROWTH,
            self::GROWTH => self::ESTABLISHED,
            self::ESTABLISHED => null, // No next stage
        };
    }

    public function getPreviousStage(): ?self
    {
        return match ($this) {
            self::IDEA_CONCEPT => null, // No previous stage
            self::STARTUP => self::IDEA_CONCEPT,
            self::GROWTH => self::STARTUP,
            self::ESTABLISHED => self::GROWTH,
        };
    }

    public function isBefore(self $stage): bool
    {
        $order = [
            self::IDEA_CONCEPT,
            self::STARTUP,
            self::GROWTH,
            self::ESTABLISHED,
        ];

        $thisIndex = array_search($this, $order);
        $otherIndex = array_search($stage, $order);

        return $thisIndex < $otherIndex;
    }

    public function isAfter(self $stage): bool
    {
        return $stage->isBefore($this);
    }
}
