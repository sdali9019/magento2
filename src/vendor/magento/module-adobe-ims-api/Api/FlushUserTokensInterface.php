<?php
/**
 * Copyright 2022 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\AdobeImsApi\Api;

/**
 * Declare functionality for remove user access and refresh tokens functionality
 * @api
 */
interface FlushUserTokensInterface
{
    /**
     * Remove access and refresh tokens for the specified user or current user
     *
     * @param int $adminUserId
     */
    public function execute(?int $adminUserId = null): void;
}
