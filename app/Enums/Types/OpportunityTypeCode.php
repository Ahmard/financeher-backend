<?php

namespace App\Enums\Types;

enum OpportunityTypeCode: string
{
    case GRANTS = 'FNC/OPPORTUNITY-TYPE/GRANTS';
    case INVESTMENTS = 'FNC/OPPORTUNITY-TYPE/INVESTMENTS';
    case LOANS = 'FNC/OPPORTUNITY-TYPE/LOANS';
    case VENTURE_CAPITAL = 'FNC/OPPORTUNITY-TYPE/VENTURE-CAPITAL';
    case MENTORSHIP = 'FNC/OPPORTUNITY-TYPE/MENTORSHIP';
    case TRAINING = 'FNC/OPPORTUNITY-TYPE/TRAINING';
    case COMPETITIONS = 'FNC/OPPORTUNITY-TYPE/COMPETITIONS';
    case PARTNERSHIPS = 'FNC/OPPORTUNITY-TYPE/PARTNERSHIPS';
    case INCUBATORS_ACCELERATORS = 'FNC/OPPORTUNITY-TYPE/INCUBATORS-ACCELERATORS';
    case MARKET_ACCESS = 'FNC/OPPORTUNITY-TYPE/MARKET-ACCESS';

    public function getDisplayName(): string
    {
        return match($this) {
            self::GRANTS => 'Grants',
            self::INVESTMENTS => 'Investments',
            self::LOANS => 'Loans',
            self::VENTURE_CAPITAL => 'Venture Capitalists',
            self::MENTORSHIP => 'Mentorship',
            self::TRAINING => 'Training',
            self::COMPETITIONS => 'Competitions',
            self::PARTNERSHIPS => 'Partnerships',
            self::INCUBATORS_ACCELERATORS => 'Incubators & Accelerators',
            self::MARKET_ACCESS => 'Market Access',
        };
    }

    public function getDescription(): string
    {
        return match($this) {
            self::GRANTS => 'Non-repayable funding opportunities provided by government agencies, foundations, non-profit organizations, and international bodies to support specific business initiatives, research projects, or social impact programs.',
            self::INVESTMENTS => 'Equity-based funding opportunities where investors provide capital in exchange for ownership stakes in businesses, including angel investments, private equity, and strategic partnerships with growth potential.',
            self::LOANS => 'Debt financing options including traditional bank loans, microfinance, peer-to-peer lending, government-backed loans, and alternative lending solutions that require repayment with interest over specified terms.',
            self::VENTURE_CAPITAL => 'Professional investment firms and venture capital funds that provide substantial funding to high-growth potential startups and early-stage companies in exchange for significant equity stakes and active involvement.',
            self::MENTORSHIP => 'Professional guidance and advisory opportunities connecting entrepreneurs with experienced business leaders, industry experts, and successful professionals who provide strategic advice and knowledge transfer.',
            self::TRAINING => 'Educational and skill development opportunities including workshops, courses, certification programs, accelerator programs, and professional development initiatives to enhance business capabilities and expertise.',
            self::COMPETITIONS => 'Business plan competitions, pitch contests, innovation challenges, and entrepreneurship competitions offering cash prizes, recognition, networking opportunities, and potential investor connections.',
            self::PARTNERSHIPS => 'Strategic business partnerships, joint ventures, collaboration opportunities, distribution agreements, and alliance programs that can accelerate growth through shared resources and market access.',
            self::INCUBATORS_ACCELERATORS => 'Structured programs providing startups with mentorship, workspace, seed funding, business development support, and access to investor networks during intensive development periods.',
            self::MARKET_ACCESS => 'Opportunities to access new markets, distribution channels, trade missions, export programs, supplier diversity initiatives, and platforms that connect businesses with potential customers or markets.',
        };
    }

    public function getCategory(): string
    {
        return match($this) {
            self::GRANTS, self::INVESTMENTS, self::VENTURE_CAPITAL, self::LOANS => 'Funding',
            self::MENTORSHIP => 'Support',
            self::TRAINING => 'Development',
            self::COMPETITIONS => 'Recognition',
            self::PARTNERSHIPS => 'Collaboration',
            self::INCUBATORS_ACCELERATORS => 'Programs',
            self::MARKET_ACCESS => 'Growth',
        };
    }

    public function getTypicalRequirements(): array
    {
        return match($this) {
            self::GRANTS => [
                'Detailed project proposal',
                'Specific eligibility criteria',
                'Social impact metrics',
                'Reporting requirements',
                'Non-profit status (sometimes)',
            ],
            self::INVESTMENTS => [
                'Business plan',
                'Financial projections',
                'Market analysis',
                'Management team credentials',
                'Scalability potential',
            ],
            self::LOANS => [
                'Credit history',
                'Collateral requirements',
                'Financial statements',
                'Repayment capacity',
                'Business registration',
            ],
            self::VENTURE_CAPITAL => [
                'High growth potential',
                'Scalable business model',
                'Strong management team',
                'Large market opportunity',
                'Exit strategy potential',
            ],
            self::MENTORSHIP => [
                'Commitment to learning',
                'Clear business objectives',
                'Willingness to accept guidance',
                'Regular meeting availability',
                'Implementation capacity',
            ],
            self::TRAINING => [
                'Training prerequisites',
                'Time commitment',
                'Learning objectives',
                'Application of skills',
                'Completion requirements',
            ],
            self::COMPETITIONS => [
                'Business plan submission',
                'Pitch presentation',
                'Innovation criteria',
                'Judging requirements',
                'Implementation feasibility',
            ],
            self::PARTNERSHIPS => [
                'Strategic alignment',
                'Complementary capabilities',
                'Mutual benefit potential',
                'Due diligence process',
                'Legal agreements',
            ],
            self::INCUBATORS_ACCELERATORS => [
                'Early-stage business',
                'Full-time commitment',
                'Equity participation',
                'Coachability',
                'Growth ambition',
            ],
            self::MARKET_ACCESS => [
                'Product readiness',
                'Market research',
                'Compliance requirements',
                'Distribution capability',
                'Cultural adaptation',
            ],
        };
    }

    public function getTypicalBenefits(): array
    {
        return match($this) {
            self::GRANTS => [
                'Non-repayable funding',
                'Credibility and validation',
                'No equity dilution',
                'Networking opportunities',
                'Impact recognition',
            ],
            self::INVESTMENTS => [
                'Growth capital',
                'Strategic guidance',
                'Network access',
                'Validation and credibility',
                'Follow-on funding potential',
            ],
            self::LOANS => [
                'Retain full ownership',
                'Predictable payments',
                'Build credit history',
                'Tax deductible interest',
                'Flexible use of funds',
            ],
            self::VENTURE_CAPITAL => [
                'Large funding amounts',
                'Industry expertise',
                'Strategic connections',
                'Operational support',
                'Exit opportunities',
            ],
            self::MENTORSHIP => [
                'Expert guidance',
                'Industry insights',
                'Network expansion',
                'Skill development',
                'Strategic advice',
            ],
            self::TRAINING => [
                'Skill enhancement',
                'Knowledge acquisition',
                'Certification credentials',
                'Networking opportunities',
                'Implementation tools',
            ],
            self::COMPETITIONS => [
                'Prize money',
                'Public recognition',
                'Media exposure',
                'Investor attention',
                'Validation',
            ],
            self::PARTNERSHIPS => [
                'Shared resources',
                'Market expansion',
                'Risk mitigation',
                'Innovation acceleration',
                'Cost optimization',
            ],
            self::INCUBATORS_ACCELERATORS => [
                'Structured support',
                'Seed funding',
                'Mentor network',
                'Workspace access',
                'Investor connections',
            ],
            self::MARKET_ACCESS => [
                'Revenue growth',
                'Geographic expansion',
                'Customer diversification',
                'Brand recognition',
                'Competitive advantage',
            ],
        };
    }

    public function getSuitableStages(): array
    {
        return match($this) {
            self::GRANTS, self::MENTORSHIP => ['Idea/Concept', 'Startup', 'Growth'],
            self::INVESTMENTS, self::VENTURE_CAPITAL => ['Startup', 'Growth'],
            self::LOANS => ['Startup', 'Growth', 'Established'],
            self::TRAINING => ['Idea/Concept', 'Startup', 'Growth', 'Established'],
            self::COMPETITIONS, self::INCUBATORS_ACCELERATORS => ['Idea/Concept', 'Startup'],
            self::PARTNERSHIPS, self::MARKET_ACCESS => ['Growth', 'Established'],
        };
    }

    public function requiresRepayment(): bool
    {
        return match($this) {
            self::LOANS => true,
            default => false,
        };
    }

    public function involvesEquityDilution(): bool
    {
        return match($this) {
            self::INVESTMENTS, self::VENTURE_CAPITAL, self::INCUBATORS_ACCELERATORS => true,
            default => false,
        };
    }

    public static function getAllCodes(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    public static function getAllDisplayNames(): array
    {
        return array_map(fn($case) => $case->getDisplayName(), self::cases());
    }

    public static function getByCategory(string $category): array
    {
        return array_filter(
            self::cases(),
            fn($case) => $case->getCategory() === $category
        );
    }

    public static function getFundingTypes(): array
    {
        return self::getByCategory('Funding');
    }

    public static function getNonFundingTypes(): array
    {
        return array_filter(
            self::cases(),
            fn($case) => $case->getCategory() !== 'Funding'
        );
    }

    public static function fromCode(string $code): ?self
    {
        return self::tryFrom($code);
    }

    public static function exists(string $code): bool
    {
        return self::tryFrom($code) !== null;
    }
}