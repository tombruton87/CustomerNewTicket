<?php
// SPDX-License-Identifier: AGPL-3.0-or-later
// Copyright (C) 2024 Hamlet Digital

namespace Modules\CustomerNewTicket\Providers;

use Illuminate\Support\ServiceProvider;

define('CNT_MODULE', 'customernewticket');

class CustomerNewTicketServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerConfig();
        $this->registerTranslations();
        $this->hooks();
    }

    public function hooks()
    {
        // Register CSS
        \Eventy::addFilter('stylesheets', function ($styles) {
            $styles[] = \Module::getPublicPath(CNT_MODULE) . '/css/module.css';
            return $styles;
        });

        // Register JS
        \Eventy::addFilter('javascripts', function ($javascripts) {
            $javascripts[] = \Module::getPublicPath(CNT_MODULE) . '/js/module.js';
            return $javascripts;
        });

        // Keep the cog menu div in the DOM (it only renders when filter returns non-empty).
        // We inject our envelope button next to it, so we need the anchor to exist.
        \Eventy::addFilter('customer.profile_menu', function ($html) {
            return $html . '<!-- cnt -->';
        }, 5, 1);

        // Inject envelope button on customer profile pages.
        // Also runs on plugin-provided tabs that reuse the core customer profile
        // layout — e.g. StoklySync's stoklysync/{id}/stokly and .../orders routes.
        // If those plugins aren't installed, the Route::is calls simply return
        // false and nothing changes.
        \Eventy::addAction('javascript', function () {
            $on_profile = \Route::is('customers.update')
                || \Route::is('customers.conversations')
                || \Route::is('stoklysync.customer.stokly')
                || \Route::is('stoklysync.customer.orders');

            if ($on_profile) {
                $customer_id = (int)(
                    \Route::current()->parameter('id')
                    ?? request()->route('id')
                    ?? 0
                );

                $customer_email = '';
                if ($customer_id) {
                    try {
                        $customer = \App\Customer::find($customer_id);
                        if ($customer) {
                            $customer_email = $customer->getMainEmail() ?? '';
                        }
                    } catch (\Exception $e) {}
                }

                $mailboxes = [];
                try {
                    $mailbox_ids = auth()->user()->mailboxesIdsCanView();
                    if (!empty($mailbox_ids)) {
                        $rows = \App\Mailbox::whereIn('id', $mailbox_ids)->orderBy('name')->get();
                        foreach ($rows as $mb) {
                            $mailboxes[] = [
                                'name' => $mb->name,
                                'url'  => route('conversations.create', ['mailbox_id' => $mb->id]),
                            ];
                        }
                    }
                } catch (\Exception $e) {}

                echo 'var cntMailboxes     = ' . json_encode($mailboxes) . ';' . "\n";
                echo 'var cntCustomerEmail = ' . json_encode($customer_email) . ';' . "\n";
                echo 'var cntLabelNewTicket = ' . json_encode(__('New Ticket')) . ';' . "\n";
                echo 'cntInitProfileButton();' . "\n";
            }

        }, 20);
    }

    public function register()
    {
        //
    }

    protected function registerConfig()
    {
        $this->mergeConfigFrom(__DIR__ . '/../Config/config.php', 'customernewticket');
    }

    protected function registerTranslations()
    {
        $this->loadJsonTranslationsFrom(__DIR__ . '/../Resources/lang');
    }

    public function provides()
    {
        return [];
    }
}
