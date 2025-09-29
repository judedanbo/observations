<?php

use App\Enums\AuditorGeneralReportStatusEnum;
use App\Enums\AuditorGeneralReportTypeEnum;
use Tests\TestCase;
use Filament\Support\Colors\Color;

test('report type enum has correct values', function () {
    $cases = AuditorGeneralReportTypeEnum::cases();
         $this->assertCount(5, $cases);
        $this->assertEquals('annual', AuditorGeneralReportTypeEnum::ANNUAL->value);
        $this->assertEquals('quarterly', AuditorGeneralReportTypeEnum::QUARTERLY->value);
        $this->assertEquals('special', AuditorGeneralReportTypeEnum::SPECIAL->value);
        $this->assertEquals('performance', AuditorGeneralReportTypeEnum::PERFORMANCE->value);
        $this->assertEquals('thematic', AuditorGeneralReportTypeEnum::THEMATIC->value);
});

test('report type enum has correct labels', function () {
    $this->assertEquals('Annual Report', AuditorGeneralReportTypeEnum::ANNUAL->getLabel());
        $this->assertEquals('Quarterly Report', AuditorGeneralReportTypeEnum::QUARTERLY->getLabel());
        $this->assertEquals('Special Investigation Report', AuditorGeneralReportTypeEnum::SPECIAL->getLabel());
        $this->assertEquals('Performance Audit Report', AuditorGeneralReportTypeEnum::PERFORMANCE->getLabel());
        $this->assertEquals('Thematic Report', AuditorGeneralReportTypeEnum::THEMATIC->getLabel());
});

test('report type enum has correct descriptions', function () {
    $this->assertStringContainsString('Comprehensive annual report', AuditorGeneralReportTypeEnum::ANNUAL->getDescription());
        $this->assertStringContainsString('Quarterly summary of audit', AuditorGeneralReportTypeEnum::QUARTERLY->getDescription());
        $this->assertStringContainsString('Special investigation or urgent', AuditorGeneralReportTypeEnum::SPECIAL->getDescription());
        $this->assertStringContainsString('Performance and value-for-money', AuditorGeneralReportTypeEnum::PERFORMANCE->getDescription());
        $this->assertStringContainsString('Focused report on specific', AuditorGeneralReportTypeEnum::THEMATIC->getDescription());
});

test('report type enum get options returns correct array', function () {
    $options = AuditorGeneralReportTypeEnum::getOptions();
         $this->assertIsArray($options);
        $this->assertCount(5, $options);
        $this->assertArrayHasKey('annual', $options);
        $this->assertArrayHasKey('quarterly', $options);
        $this->assertArrayHasKey('special', $options);
        $this->assertArrayHasKey('performance', $options);
        $this->assertArrayHasKey('thematic', $options);
         $this->assertEquals('Annual Report', $options['annual']);
        $this->assertEquals('Quarterly Report', $options['quarterly']);
});

test('status enum has correct values', function () {
    $cases = AuditorGeneralReportStatusEnum::cases();
         $this->assertCount(4, $cases);
        $this->assertEquals('draft', AuditorGeneralReportStatusEnum::DRAFT->value);
        $this->assertEquals('under_review', AuditorGeneralReportStatusEnum::UNDER_REVIEW->value);
        $this->assertEquals('approved', AuditorGeneralReportStatusEnum::APPROVED->value);
        $this->assertEquals('published', AuditorGeneralReportStatusEnum::PUBLISHED->value);
});

test('status enum has correct labels', function () {
    $this->assertEquals('Draft', AuditorGeneralReportStatusEnum::DRAFT->getLabel());
        $this->assertEquals('Under Review', AuditorGeneralReportStatusEnum::UNDER_REVIEW->getLabel());
        $this->assertEquals('Approved', AuditorGeneralReportStatusEnum::APPROVED->getLabel());
        $this->assertEquals('Published', AuditorGeneralReportStatusEnum::PUBLISHED->getLabel());
});

test('status enum has correct colors', function () {
    $this->assertEquals(Color::Gray, AuditorGeneralReportStatusEnum::DRAFT->getColor());
        $this->assertEquals(Color::Amber, AuditorGeneralReportStatusEnum::UNDER_REVIEW->getColor());
        $this->assertEquals(Color::Green, AuditorGeneralReportStatusEnum::APPROVED->getColor());
        $this->assertEquals(Color::Blue, AuditorGeneralReportStatusEnum::PUBLISHED->getColor());
});

test('status enum has correct icons', function () {
    $this->assertEquals('heroicon-o-document', AuditorGeneralReportStatusEnum::DRAFT->getIcon());
        $this->assertEquals('heroicon-o-clock', AuditorGeneralReportStatusEnum::UNDER_REVIEW->getIcon());
        $this->assertEquals('heroicon-o-check-circle', AuditorGeneralReportStatusEnum::APPROVED->getIcon());
        $this->assertEquals('heroicon-o-globe-alt', AuditorGeneralReportStatusEnum::PUBLISHED->getIcon());
});

test('status enum get options returns correct array', function () {
    $options = AuditorGeneralReportStatusEnum::getOptions();
         $this->assertIsArray($options);
        $this->assertCount(4, $options);
        $this->assertArrayHasKey('draft', $options);
        $this->assertArrayHasKey('under_review', $options);
        $this->assertArrayHasKey('approved', $options);
        $this->assertArrayHasKey('published', $options);
         $this->assertEquals('Draft', $options['draft']);
        $this->assertEquals('Under Review', $options['under_review']);
        $this->assertEquals('Approved', $options['approved']);
        $this->assertEquals('Published', $options['published']);
});

test('status enum workflow validation works', function () {
    // Draft can transition to Under Review
        $this->assertTrue(AuditorGeneralReportStatusEnum::DRAFT->canTransitionTo(AuditorGeneralReportStatusEnum::UNDER_REVIEW));
         // Under Review can transition to Approved or back to Draft
        $this->assertTrue(AuditorGeneralReportStatusEnum::UNDER_REVIEW->canTransitionTo(AuditorGeneralReportStatusEnum::APPROVED));
        $this->assertTrue(AuditorGeneralReportStatusEnum::UNDER_REVIEW->canTransitionTo(AuditorGeneralReportStatusEnum::DRAFT));
         // Approved can transition to Published
        $this->assertTrue(AuditorGeneralReportStatusEnum::APPROVED->canTransitionTo(AuditorGeneralReportStatusEnum::PUBLISHED));
         // Published cannot transition anywhere
        $this->assertFalse(AuditorGeneralReportStatusEnum::PUBLISHED->canTransitionTo(AuditorGeneralReportStatusEnum::DRAFT));
        $this->assertFalse(AuditorGeneralReportStatusEnum::PUBLISHED->canTransitionTo(AuditorGeneralReportStatusEnum::UNDER_REVIEW));
        $this->assertFalse(AuditorGeneralReportStatusEnum::PUBLISHED->canTransitionTo(AuditorGeneralReportStatusEnum::APPROVED));
         // Invalid transitions
        $this->assertFalse(AuditorGeneralReportStatusEnum::DRAFT->canTransitionTo(AuditorGeneralReportStatusEnum::APPROVED));
        $this->assertFalse(AuditorGeneralReportStatusEnum::DRAFT->canTransitionTo(AuditorGeneralReportStatusEnum::PUBLISHED));
});

test('status enum editable states are correct', function () {
    $this->assertTrue(AuditorGeneralReportStatusEnum::DRAFT->isEditable());
        $this->assertTrue(AuditorGeneralReportStatusEnum::UNDER_REVIEW->isEditable());
        $this->assertFalse(AuditorGeneralReportStatusEnum::APPROVED->isEditable());
        $this->assertFalse(AuditorGeneralReportStatusEnum::PUBLISHED->isEditable());
});

test('enum values can be used in match expressions', function () {
    $status = AuditorGeneralReportStatusEnum::DRAFT;
         $result = match ($status) {
            AuditorGeneralReportStatusEnum::DRAFT => 'draft_action',
            AuditorGeneralReportStatusEnum::UNDER_REVIEW => 'review_action',
            AuditorGeneralReportStatusEnum::APPROVED => 'approved_action',
            AuditorGeneralReportStatusEnum::PUBLISHED => 'published_action',
        };
         $this->assertEquals('draft_action', $result);
});

test('enum values can be serialized and unserialized', function () {
    $status = AuditorGeneralReportStatusEnum::UNDER_REVIEW;
        $type = AuditorGeneralReportTypeEnum::ANNUAL;
         $serialized = serialize([$status, $type]);
        $unserialized = unserialize($serialized);
         $this->assertEquals($status, $unserialized[0]);
        $this->assertEquals($type, $unserialized[1]);
});
