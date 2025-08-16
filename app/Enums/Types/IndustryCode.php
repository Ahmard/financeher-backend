<?php

namespace App\Enums\Types;

use App\Enums\EnumHelper\DBCompatibleEnumInterface;
use App\Enums\EnumHelper\DBCompatibleEnumTrait;

enum IndustryCode: string implements DBCompatibleEnumInterface
{
    use DBCompatibleEnumTrait;

    case EDUCATION = 'FNC/BUSINESS-TYPE/EDUCATION';
    case AGRICULTURE = 'FNC/BUSINESS-TYPE/AGRICULTURE';
    case RENEWABLES = 'FNC/BUSINESS-TYPE/RENEWABLES';
    case HEALTHCARE = 'FNC/BUSINESS-TYPE/HEALTHCARE';
    case FINANCIAL_SERVICES = 'FNC/BUSINESS-TYPE/FINANCIAL-SERVICES';
    case TECHNOLOGY = 'FNC/BUSINESS-TYPE/TECHNOLOGY';
    case MANUFACTURING = 'FNC/BUSINESS-TYPE/MANUFACTURING';
    case RETAIL_ECOMMERCE = 'FNC/BUSINESS-TYPE/RETAIL-ECOMMERCE';
    case CONSTRUCTION_REAL_ESTATE = 'FNC/BUSINESS-TYPE/CONSTRUCTION-REAL-ESTATE';
    case TRANSPORTATION_LOGISTICS = 'FNC/BUSINESS-TYPE/TRANSPORTATION-LOGISTICS';
    case HOSPITALITY_TOURISM = 'FNC/BUSINESS-TYPE/HOSPITALITY-TOURISM';
    case MEDIA_ENTERTAINMENT = 'FNC/BUSINESS-TYPE/MEDIA-ENTERTAINMENT';
    case PROFESSIONAL_SERVICES = 'FNC/BUSINESS-TYPE/PROFESSIONAL-SERVICES';
    case ENERGY_UTILITIES = 'FNC/BUSINESS-TYPE/ENERGY-UTILITIES';
    case NON_PROFIT_SOCIAL = 'FNC/BUSINESS-TYPE/NON-PROFIT-SOCIAL';
    case OTHERS = 'FNC/BUSINESS-TYPE/OTHERS';

    public function getDisplayName(): string
    {
        return match ($this) {
            self::EDUCATION => 'Education',
            self::AGRICULTURE => 'Agriculture',
            self::RENEWABLES => 'Renewables',
            self::HEALTHCARE => 'Healthcare',
            self::FINANCIAL_SERVICES => 'Financial Services',
            self::TECHNOLOGY => 'Technology',
            self::MANUFACTURING => 'Manufacturing',
            self::RETAIL_ECOMMERCE => 'Retail & E-commerce',
            self::CONSTRUCTION_REAL_ESTATE => 'Construction & Real Estate',
            self::TRANSPORTATION_LOGISTICS => 'Transportation & Logistics',
            self::HOSPITALITY_TOURISM => 'Hospitality & Tourism',
            self::MEDIA_ENTERTAINMENT => 'Media & Entertainment',
            self::PROFESSIONAL_SERVICES => 'Professional Services',
            self::ENERGY_UTILITIES => 'Energy & Utilities',
            self::NON_PROFIT_SOCIAL => 'Non-Profit & Social Enterprise',
            self::OTHERS => 'Others',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::EDUCATION => 'Educational institutions including schools, colleges, universities, training centers, and online learning platforms that provide formal or informal education services to students of all ages.',
            self::AGRICULTURE => 'Agricultural businesses involved in crop cultivation, livestock farming, aquaculture, forestry, and related activities including farm equipment, agricultural technology, and food processing operations.',
            self::RENEWABLES => 'Renewable energy companies specializing in solar, wind, hydroelectric, geothermal, and biomass energy generation, as well as energy storage solutions and sustainable technology development.',
            self::HEALTHCARE => 'Healthcare organizations including hospitals, clinics, medical practices, pharmaceutical companies, medical device manufacturers, telehealth services, and wellness programs focused on patient care and medical services.',
            self::FINANCIAL_SERVICES => 'Financial institutions and services including banks, credit unions, investment firms, insurance companies, fintech startups, payment processors, and wealth management companies.',
            self::TECHNOLOGY => 'Technology companies including software development, IT services, cybersecurity, cloud computing, artificial intelligence, mobile applications, and digital transformation solutions.',
            self::MANUFACTURING => 'Manufacturing businesses engaged in the production of goods including automotive, electronics, textiles, machinery, consumer products, and industrial equipment manufacturing.',
            self::RETAIL_ECOMMERCE => 'Retail businesses including physical stores, online marketplaces, e-commerce platforms, fashion retailers, grocery stores, and specialty retail outlets serving consumer markets.',
            self::CONSTRUCTION_REAL_ESTATE => 'Construction companies, real estate developers, property management firms, architectural services, engineering consultancies, and infrastructure development businesses.',
            self::TRANSPORTATION_LOGISTICS => 'Transportation and logistics companies including shipping, freight, courier services, ride-sharing, public transit, supply chain management, and warehouse operations.',
            self::HOSPITALITY_TOURISM => 'Hospitality businesses including hotels, restaurants, travel agencies, tour operators, event management, entertainment venues, and tourism-related services.',
            self::MEDIA_ENTERTAINMENT => 'Media and entertainment companies including film production, music, gaming, publishing, broadcasting, digital content creation, and streaming services.',
            self::PROFESSIONAL_SERVICES => 'Professional service firms including legal services, accounting, consulting, marketing agencies, human resources, business advisory, and specialized professional expertise.',
            self::ENERGY_UTILITIES => 'Traditional energy and utility companies including oil and gas, electric utilities, water and wastewater services, telecommunications infrastructure, and utility management.',
            self::NON_PROFIT_SOCIAL => 'Non-profit organizations, charities, social enterprises, community organizations, foundations, and mission-driven businesses focused on social impact and community benefit.',
            self::OTHERS => 'Businesses and organizations that do not fit into the standard industry categories, including unique business models, emerging industries, and specialized niche markets.',
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
}
