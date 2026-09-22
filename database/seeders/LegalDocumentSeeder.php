<?php

namespace Database\Seeders;

use App\Models\LegalDocument;
use Illuminate\Database\Seeder;

class LegalDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documents = [
            [
                'type' => 'terms',
                'slug' => 'terms-and-conditions',
                'title' => 'Terms and Conditions',
                'version' => '1.0',
                'effective_date' => '2026-09-01',
                'status' => 'active',
                'sort_order' => 1,
                'content' => "<h3>1. Agreement to Terms</h3>
<p>These Terms and Conditions constitute a legally binding agreement between you and EMAC Development, LLC. regarding your access to and use of our website, estimation tools, house plan catalog, plumbing services, and general contracting operations across Florida, Jamaica, and the Cayman Islands.</p>

<h3>2. Architectural House Plans & Intellectual Property</h3>
<p>All stock house plans, blueprints, 3D renderings, and schematic illustrations provided by EMAC Development are proprietary intellectual property:</p>
<ul>
    <li>Purchasing a house plan grants a single-build license for one residential property unless otherwise contracted in writing.</li>
    <li>Resale, unauthorized duplication, or redistribution of EMAC blueprints is strictly prohibited.</li>
    <li>Plan modifications must be approved or executed through EMAC's architectural engineering team.</li>
</ul>

<h3>3. Contracting, Estimates & Service Execution</h3>
<ul>
    <li>Online quotes and preliminary estimates are provided in good faith and finalized following on-site evaluation or verified engineering reviews.</li>
    <li>Work performed in Florida is governed by Florida construction licensing statutes (Certified General Contractor & Certified Plumbing Contractor).</li>
    <li>Work performed in the Cayman Islands and Jamaica complies with local building codes, trade licenses, and planning board stipulations.</li>
</ul>

<h3>4. Handyman & Property Maintenance Subscriptions</h3>
<p>Subscription maintenance contracts renew automatically per agreement terms. Cancellations require 30 days written notice. Emergency call-outs outside standard operating hours are dispatched according to subscription tier coverage.</p>

<h3>5. Governing Law & Jurisdiction</h3>
<p>These terms shall be governed by and construed in accordance with the laws of the applicable operational jurisdiction in which the services are rendered.</p>",
            ],
            [
                'type' => 'privacy',
                'slug' => 'privacy-policy',
                'title' => 'Privacy Policy',
                'version' => '1.0',
                'effective_date' => '2026-09-01',
                'status' => 'active',
                'sort_order' => 2,
                'content' => '<h3>1. Information We Collect</h3>
<p>EMAC Development collects personal identification details (name, email address, phone number, physical site address, property dimensions) provided when submitting service inquiries, requesting construction estimates, or subscribing to handyman maintenance services.</p>

<h3>2. Use of Information</h3>
<p>Collected information is utilized strictly to:</p>
<ul>
    <li>Provide detailed architectural estimations, structural blueprints, and contracting proposals.</li>
    <li>Dispatch field technicians and general contractor crews to your project site.</li>
    <li>Process subscription billing and provide maintenance status notifications.</li>
    <li>Comply with regional building department regulations in Florida, Jamaica, and the Cayman Islands.</li>
</ul>

<h3>3. Data Protection & Security</h3>
<p>We implement industry-standard encryption, SSL protocols, and restricted access controls. Your project details, house plans, and payment records are protected from unauthorized access.</p>

<h3>4. Third-Party Disclosures</h3>
<p>We do not sell or trade your personal information. Information is shared only with verified trade subcontractors, structural engineers, and permitting authorities strictly necessary to execute your contracted build.</p>

<h3>5. Contact Us</h3>
<p>For privacy inquiries or data removal requests, contact our compliance team at privacy@emacdevelopment.com.</p>',
            ],
        ];

        foreach ($documents as $doc) {
            LegalDocument::updateOrCreate(
                ['type' => $doc['type']],
                $doc
            );
        }
    }
}
