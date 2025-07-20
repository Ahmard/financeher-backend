<?php

namespace App\Enums;

enum SystemSettingDefinition: string
{
    case APP_VERSION = 'app_version';
    case PAYMENT_GATEWAY = 'payment_gateway';

    case SYSTEM_STATUS = 'system_status';

    case LOGIN_MODULE_STATUS = 'login_module_status';
    case REGISTER_MODULE_STATUS = 'register_module_status';
    case MAIL_MODULE_STATUS = 'mail_module_status';
    case WALLET_MODULE_STATUS = 'wallet_module_status';
    case PAYMENT_MODULE_STATUS = 'payment_module_status';

    case MONIEPOINT_AUTH_TOKEN = 'moniepoint_auth_token';
    case REMITTA_AUTH_TOKEN = 'remitta_auth_token';
    case MONIEPOINT_VAT = 'moniepoint_vat';
    case MONIEPOINT_CARD_CHARGES = 'moniepoint_card_charges';
    case MONIEPOINT_TRANSFER_CHARGES = 'moniepoint_transfer_charges';
    case MONIEPOINT_VAN_TRANSFER_CHARGES = 'moniepoint_van_transfer_charges';

    case MAX_ONBOARDING_APPROVAL_LEVEL = 'max_onboarding_approval_level';

    case WALLET_BILLABLE_ENTITY = 'wallet_billable_entity';

    case SYSTEM_MAINTENANCE_MESSAGE = 'system_maintenance_message';
    case MODULE_MAINTENANCE_MESSAGE = 'module_maintenance_message';
}
