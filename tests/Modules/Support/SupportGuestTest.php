<?php

declare(strict_types=1);

use APIHelper\Request;
use PHPUnit\Framework\TestCase;

final class SupportGuestTest extends TestCase
{
    public function testTicketCreateForGuest()
    {
        $result = Request::makeRequest('guest/support/ticket_create', [
            'name' => 'Name',
            'email' => 'email@example.com',
            'subject' => 'Subject',
            'message' => 'message',
        ]);

        $this->assertTrue($result->wasSuccessful(), $result->generatePHPUnitMessage());
        $this->assertIsString($result->getResult());
        $this->assertGreaterThanOrEqual(200, strlen($result->getResult()));
        $this->assertLessThanOrEqual(255, strlen($result->getResult()));
    }

    public function testTicketCreateForGuestDisabled()
    {
        // Disable public tickets
        Request::makeRequest('admin/extension/config_save', ['ext' => 'mod_support', 'disable_public_tickets' => true]);

        // Now ensure
        $result = Request::makeRequest('guest/support/ticket_create', [
            'name' => 'Name',
            'email' => 'email2@example.com',
            'subject' => 'Subject',
            'message' => 'message',
        ]);

        $this->assertFalse($result->wasSuccessful());
        $this->assertEquals("We currently aren't accepting support tickets from unregistered users. Please use another contact method.", $result->getErrorMessage());

        // Set it back
        Request::makeRequest('admin/extension/config_save', ['ext' => 'mod_support', 'disable_public_tickets' => false]);
    }
}