<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Usercheck;
use App\Models\Uorder;

class AdminPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('admin')->user();

        // 1. If not authenticated as admin, let admin.auth handle it or redirect.
        if (!$user) {
            return $next($request);
        }

        // 2. Super Admin bypass (section == 1 has absolute bypass)
        if ($user->section == 1) {
            return $next($request);
        }

        // Get the current route name
        $routeName = $request->route()->getName();

        // 3. Exempted Routes (Dashboard, Logout, Own Setting Admin edits)
        $exemptRoutes = [
            'admin.dashboard',
            'admin.expense.store', // Quick dashboard action
            'admin.logout',
            'admin.settings.index',
            'admin.settings.username',
            'admin.settings.password',
        ];

        if ($routeName && in_array($routeName, $exemptRoutes)) {
            return $next($request);
        }

        // 4. Profit & Loss Report: Strict restriction to Super Administrators
        if ($routeName && str_starts_with($routeName, 'admin.reports.pl')) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied: Profit & Loss report is restricted to Super Administrators.');
        }

        // Fetch Usercheck record
        $usercheck = Usercheck::where('uid', $user->user_id)->first();
        if (!$usercheck) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied: Permissions not configured for your account.');
        }

        // 5. Dynamic & Static Route-to-Column Mapping
        $column = null;

        if ($routeName === 'admin.products.index') {
            $column = ($request->query('action') === 'add') ? 'prod' : 'mprod';
        } elseif ($routeName === 'admin.sales.index') {
            $column = ($request->query('view') === 'list') ? 'linvc' : 'minv';
        } elseif ($routeName && str_starts_with($routeName, 'admin.online_orders.')) {
            $status = $request->query('status');
            if ($status) {
                if ($status === 'sending') $column = 'sord';
                elseif ($status === 'delivered') $column = 'dord';
                elseif ($status === 'cancelled') $column = 'cord';
                else $column = 'ord';
            } else {
                // For edit/update/print/payment/status post routes, find the order status
                $orderId = $request->route('order');
                if ($orderId) {
                    $order = Uorder::find($orderId);
                    if ($order) {
                        $ostatus = $order->ostatus ?? 'p';
                        if ($ostatus === 's') $column = 'sord';
                        elseif ($ostatus === 'd') $column = 'dord';
                        elseif ($ostatus === 'c') $column = 'cord';
                        else $column = 'ord';
                    }
                }
            }
            if (!$column) {
                $column = 'ord'; // Default fallback
            }
        } elseif ($routeName && str_starts_with($routeName, 'admin.quotations.')) {
            // Quotations and Estimations share the same controller
            // Check if user has permission for either Quotations or Estimations depending on index or creation
            if (in_array($routeName, ['admin.quotations.create', 'admin.quotations.store'])) {
                if ($usercheck->quot == 1 || $usercheck->estm == 1) {
                    return $next($request);
                }
                return redirect()->route('admin.dashboard')->with('error', 'Access Denied: You do not have permission to create Quotations or Estimations.');
            } else {
                if ($usercheck->mquot == 1 || $usercheck->mestm == 1) {
                    return $next($request);
                }
                return redirect()->route('admin.dashboard')->with('error', 'Access Denied: You do not have permission to manage Quotations or Estimations.');
            }
        } else {
            // Static mapping dictionary
            $routePermissionMap = [
                // Category, Subcategory, Brand
                'admin.categories.index' => 'cat',
                'admin.categories.store' => 'cat',
                'admin.categories.update' => 'cat',
                'admin.categories.destroy' => 'cat',

                'admin.subcategories.index' => 'scat',
                'admin.subcategories.store' => 'scat',
                'admin.subcategories.update' => 'scat',
                'admin.subcategories.destroy' => 'scat',

                'admin.brands.index' => 'brand',
                'admin.brands.store' => 'brand',
                'admin.brands.update' => 'brand',
                'admin.brands.destroy' => 'brand',

                // Products & Barcode
                'admin.products.store' => 'prod',
                'admin.products.update' => 'mprod',
                'admin.products.destroy' => 'mprod',
                'admin.products.barcode' => 'mprod',
                'admin.products.barcode.print' => 'mprod',
                'admin.products.stock_list' => 'slist',
                'admin.products.price_search' => 'sprice',
                'admin.products.price_search.details' => 'sprice',

                // Purchases
                'admin.purchases.create' => 'purc',
                'admin.purchases.store' => 'purc',
                'admin.purchases.index' => 'mpurc',
                'admin.purchases.edit' => 'mpurc',
                'admin.purchases.update' => 'mpurc',
                'admin.purchases.destroy' => 'mpurc',
                'admin.purchases.print' => 'mpurc',
                'admin.purchases.payment' => 'mpurc',

                // Purchase Stock (Add Stock)
                'admin.purchases.stock.index' => 'astock',
                'admin.purchases.stock.detail' => 'astock',
                'admin.purchases.stock.update' => 'astock',

                // Customer Sales/Invoices
                'admin.sales.create' => 'cinv',
                'admin.sales.store' => 'cinv',
                'admin.sales.edit' => 'minv',
                'admin.sales.update' => 'minv',
                'admin.sales.destroy' => 'minv',
                'admin.sales.print' => 'minv',
                'admin.sales.payment' => 'minv',

                // StateGST is managed with Customer Invoices
                'admin.stategst.index' => 'cinv',
                'admin.stategst.store' => 'cinv',
                'admin.stategst.update' => 'cinv',
                'admin.stategst.destroy' => 'cinv',

                // Expenses categories and expenses
                'admin.expenses.categories.index' => 'expd',
                'admin.expenses.categories.store' => 'expd',
                'admin.expenses.categories.update' => 'expd',
                'admin.expenses.categories.destroy' => 'expd',

                'admin.expenses.index' => 'expen',
                'admin.expenses.store' => 'expen',
                'admin.expenses.update' => 'expen',
                'admin.expenses.destroy' => 'expen',

                'admin.agents.index' => 'agent',
                'admin.agents.store' => 'agent',
                'admin.agents.update' => 'agent',
                'admin.agents.destroy' => 'agent',

                'admin.agents_payments.index' => 'apay',
                'admin.agents_payments.store' => 'apay',
                'admin.agents_payments.update' => 'apay',
                'admin.agents_payments.destroy' => 'apay',

                // Reports
                'admin.reports.index' => 'areport',
                'admin.reports.print' => 'areport',
                'admin.reports.excel' => 'areport',

                'admin.reports.billwise' => 'breport',
                'admin.reports.billwise.fetch' => 'breport',
                'admin.reports.billwise.print_sale' => 'breport',
                'admin.reports.billwise.print_purchase' => 'breport',

                'admin.reports.sales' => 'sreport',
                'admin.reports.sales.fetch_customers' => 'sreport',
                'admin.reports.sales.generate_type' => 'sreport',
                'admin.reports.sales.generate_name' => 'sreport',

                'admin.reports.pending' => 'preport',
                'admin.reports.pending.generate' => 'preport',

                'admin.reports.stock' => 'stockr',
                'admin.reports.stock.generate' => 'stockr',

                'admin.reports.payhistory' => 'phistory',
                'admin.reports.payhistory.generate' => 'phistory',

                'admin.reports.excel_panel' => 'excel',

                // Settings & users
                'admin.users.index' => 'auser',
                'admin.users.store' => 'auser',
                'admin.users.update' => 'auser',
                'admin.users.destroy' => 'auser',
                'admin.users.toggle_status' => 'auser',

                'admin.usersettings.index' => 'usett',
                'admin.usersettings.update' => 'usett',

                'admin.customers.index' => 'csett',
                'admin.customers.store' => 'csett',
                'admin.customers.update' => 'csett',
                'admin.customers.destroy' => 'csett',
                'admin.customers.toggle_status' => 'csett',

                'admin.backups.index' => 'backup',
                'admin.backups.create' => 'backup',
                'admin.backups.download' => 'backup',
                'admin.backups.destroy' => 'backup',
                'admin.backups.restore' => 'restore',
                'admin.backups.upload_restore' => 'restore',
            ];

            if ($routeName && isset($routePermissionMap[$routeName])) {
                $column = $routePermissionMap[$routeName];
            }
        }

        // 6. Check and Enforce Permission Column
        if ($column) {
            if (!isset($usercheck->{$column}) || $usercheck->{$column} != 1) {
                return redirect()->route('admin.dashboard')->with('error', 'Access Denied: You do not have permission to access that module.');
            }
        }

        return $next($request);
    }
}
