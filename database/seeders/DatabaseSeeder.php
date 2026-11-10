<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Invoice;
use App\Models\JobTicket;
use App\Models\Lead;
use App\Models\Product;
use App\Models\ServiceRequest;
use App\Models\Supplier;
use App\Models\Technician;
use App\Models\WorkOrder;
use App\Services\AiQuoteService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::query()->updateOrCreate(
            ['code' => 'HQ'],
            ['name' => 'HvacQuoteCraft HQ', 'city' => 'Dallas', 'phone' => '+1-214-555-0100', 'is_active' => true]
        );

        $roles = [
            'super_admin' => ['Super Admin', 'admin@demo.local'],
            'owner' => ['HVAC Owner', 'owner@demo.local'],
            'manager' => ['Operations Lead', 'manager@demo.local'],
            'accountant' => ['Estimator', 'estimator@demo.local'],
            'staff' => ['Office Staff', 'staff@demo.local'],
            'technician' => ['Field Technician', 'tech@demo.local'],
            'customer' => ['Customer Portal', 'customer@demo.local'],
        ];

        foreach ($roles as $role => [$name, $email]) {
            \Spatie\Permission\Models\Role::findOrCreate($role);
            \App\Models\User::query()->updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => 'password',
                    'user_type' => $role,
                    'branch_id' => $branch->id,
                    'is_active' => true,
                ]
            )->assignRole($role);
        }

        $customer = Customer::query()->updateOrCreate(
            ['code' => 'C-1001'],
            ['name' => 'Summit Property Mgmt', 'city' => 'Dallas', 'email' => 'ops@summit.demo', 'phone' => '+1-214-555-1001', 'balance' => 0, 'is_active' => true]
        );
        Customer::query()->updateOrCreate(
            ['code' => 'C-1002'],
            ['name' => 'Lakeview Residences', 'city' => 'Plano', 'email' => 'facilities@lakeview.demo', 'phone' => '+1-972-555-1002', 'balance' => 0, 'is_active' => true]
        );

        Supplier::query()->updateOrCreate(['code' => 'S-1001'], ['name' => 'Comfort Parts Supply', 'city' => 'Fort Worth', 'is_active' => true]);
        Lead::query()->updateOrCreate(['code' => 'L-1001'], ['name' => 'New condo heat pump bid', 'stage' => 'qualified', 'value' => 28500, 'is_active' => true]);
        Product::query()->updateOrCreate(['code' => 'SKU-FILTER'], ['name' => 'MERV-13 Filter Pack', 'sku' => 'FIL-13', 'price' => 48, 'cost' => 22, 'stock' => 120, 'reorder_level' => 20, 'is_active' => true]);
        Product::query()->updateOrCreate(['code' => 'SKU-HP'], ['name' => '16 SEER Heat Pump 3-Ton', 'sku' => 'HP-16-3T', 'price' => 5850, 'cost' => 3900, 'stock' => 6, 'reorder_level' => 2, 'is_active' => true]);
        Employee::query()->updateOrCreate(['code' => 'E-1001'], ['name' => 'Office Coordinator', 'department' => 'Operations', 'designation' => 'Dispatcher', 'salary' => 52000, 'is_active' => true]);
        WorkOrder::query()->updateOrCreate(['code' => 'WO-1001'], ['name' => 'Shop coil clean', 'status' => 'open', 'priority' => 'normal', 'notes' => 'Internal shop work', 'is_active' => true]);

        $tech = Technician::query()->updateOrCreate(
            ['code' => 'T-1001'],
            ['name' => 'Jordan Blake', 'email' => 'jordan@demo.local', 'phone' => '+1-214-555-2001', 'license_no' => 'TX-HVAC-88421', 'specialty' => 'Heat pump & rooftop', 'status' => 'available', 'is_active' => true]
        );
        Technician::query()->updateOrCreate(
            ['code' => 'T-1002'],
            ['name' => 'Sam Rivera', 'email' => 'sam@demo.local', 'phone' => '+1-214-555-2002', 'license_no' => 'TX-HVAC-77102', 'specialty' => 'Residential AC', 'status' => 'on_job', 'is_active' => true]
        );

        $sr = ServiceRequest::query()->updateOrCreate(
            ['code' => 'SR-1001'],
            [
                'name' => 'Heat pump replacement + duct sealing',
                'customer_id' => $customer->id,
                'priority' => 'high',
                'status' => 'qualified',
                'equipment' => '3-ton heat pump',
                'address' => '400 Commerce St, Dallas TX',
                'requested_at' => now()->subDays(2),
                'notes' => 'Customer wants high-efficiency heat pump package with duct sealing.',
                'is_active' => true,
            ]
        );

        ServiceRequest::query()->updateOrCreate(
            ['code' => 'SR-1002'],
            [
                'name' => 'Rooftop unit quote — Suite 210',
                'customer_id' => Customer::query()->where('code', 'C-1002')->value('id'),
                'priority' => 'normal',
                'status' => 'intake',
                'equipment' => '7.5-ton RTU',
                'address' => '210 Legacy Dr, Plano TX',
                'requested_at' => now()->subDay(),
                'notes' => 'Replace aging rooftop unit; include crane day estimate.',
                'is_active' => true,
            ]
        );

        JobTicket::query()->updateOrCreate(
            ['code' => 'JT-1001'],
            [
                'name' => 'Site survey — Commerce heat pump',
                'service_request_id' => $sr->id,
                'customer_id' => $customer->id,
                'technician_id' => $tech->id,
                'status' => 'completed',
                'priority' => 'high',
                'labor_hours' => 1.5,
                'parts_total' => 0,
                'labor_total' => 185.00,
                'scheduled_at' => now()->subDay(),
                'completed_at' => now()->subHours(8),
                'notes' => 'Measured existing ductwork; confirmed 3-ton heat pump fit.',
                'is_active' => true,
            ]
        );

        $quoting = app(AiQuoteService::class);
        $estimate = \App\Models\Estimate::query()->where('code', 'EST-DEMO1')->first();
        if (! $estimate) {
            $estimate = $quoting->draftEstimateFromIntake($sr);
            $estimate->update([
                'code' => 'EST-DEMO1',
                'messaging_consent' => true,
                'technician_id' => $tech->id,
                'branch_id' => $branch->id,
            ]);
            $quoting->approveEstimate($estimate->fresh(), 'HVAC Owner');
            $proposal = $quoting->buildProposal($estimate->fresh(), 'HVAC Owner');
            $proposal->update(['code' => 'PRO-DEMO1']);
            $quoting->approveAndSendProposal($proposal->fresh(), 'HVAC Owner');
            $followup = $quoting->draftFollowup($proposal->fresh());
            $followup->update(['consent_ok' => true, 'code' => 'FU-DEMO1']);
        }

        Invoice::query()->updateOrCreate(
            ['code' => 'INV-OPTIONAL'],
            [
                'name' => 'Optional post-acceptance invoice stub',
                'customer_id' => $customer->id,
                'branch_id' => $branch->id,
                'total' => 0,
                'status' => 'draft',
                'issued_at' => now(),
                'notes' => 'Billing module off by default — convert accepted estimates here.',
                'is_active' => true,
            ]
        );
    }
}
