<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Request;

/**
 * منوی کناری پنل — بر اساس نقش کاربر، بدون آیتم‌های اضافی و گیج‌کننده.
 */
final class AdminSidebar
{
  /**
   * @return list<array{label: string, items: list<array<string, mixed>>, footer?: bool}>
   */
  public static function sections(User $user): array
  {
    $sections = [];

    $isSuper = $user->isSuperAdmin();
    $isAdmin = $user->isAdmin() || $isSuper;
    $isReception = $user->isReceptionist();
    $isTech = $user->isTechnician();
    $isWarehouse = $user->isWarehouseManager();
    $isAccountant = $user->isAccountant();
    $isAccountantOnly = $isAccountant && ! $isAdmin && ! $isReception;
    $isTechOnly = $isTech && ! $isAdmin && ! $isReception;
    $canAccounting = $user->canManageAccounting();
    $canPos = $user->canAccessPos();
    $canInventory = $isWarehouse || $isAdmin;
    $canProducts = $user->canManageProducts();
    $canCustomers = ($isReception || $isAdmin) && ! $isAccountantOnly;
    $canReceptionOps = ($isReception || $isAdmin) && ! $isAccountantOnly;
    $canRepairOps = $isTech || $isReception || $isAdmin;
    $canBankDevices = ($canReceptionOps || ($canRepairOps && ! $isAccountantOnly)) && ! $isAccountantOnly;

    $homeItems = [];
    if ($user->isEmployee()) {
      $homeItems[] = self::item('automation.dashboard', 'میز کار', 'layout-dashboard', 'automation.dashboard', exclude: [
        'automation.dashboard.reception',
        'automation.dashboard.repair',
        'automation.dashboard.accounting',
        'automation.dashboard.bank',
      ]);
    }
    if ($isSuper) {
      $homeItems[] = self::item('super-admin.dashboard', 'مدیریت کل سیستم', 'shield-lock', 'super-admin.*');
    }
    if ($homeItems !== []) {
      $sections[] = self::section('شروع', $homeItems);
    }

    $cartableItems = [];
    if ($canReceptionOps) {
      $cartableItems[] = self::item('automation.dashboard.reception', 'کارتابل پذیرش', 'clipboard-list', 'automation.dashboard.reception');
    }
    if ($canRepairOps) {
      $cartableItems[] = self::item('automation.dashboard.repair', 'کارتابل تعمیرات', 'tool', 'automation.dashboard.repair');
    }
    if ($canAccounting) {
      $cartableItems[] = self::item('automation.dashboard.accounting', 'کارتابل حسابداری', 'calculator', 'automation.dashboard.accounting');
    }
    if ($canBankDevices) {
      $cartableItems[] = self::item('automation.dashboard.bank', 'بانک دستگاه‌ها', 'database', 'automation.dashboard.bank');
    }
    if ($cartableItems !== []) {
      $sections[] = self::section('کار امروز', $cartableItems);
    }

    if ($isTechOnly) {
      $sections[] = self::section('تعمیرات من', [
        self::item(
          'automation.repairs.index',
          'کارتابل فنی',
          'list-check',
          'automation.repairs.*',
          query: ['view' => 'my_repairs'],
        ),
      ]);
    }

    if ($canReceptionOps) {
      $receptionItems = [
        self::item('automation.service-orders.create', 'ثبت پذیرش جدید', 'file-plus', 'automation.service-orders.create'),
        self::item('automation.service-orders.index', 'همه پذیرش‌ها', 'list-details', 'automation.service-orders.index'),
      ];
      if ($isAdmin) {
        $receptionItems[] = self::item('automation.repairs.index', 'تعمیرات در جریان', 'tool', 'automation.repairs.*');
      }
      $sections[] = self::section('پذیرش و سفارش', $receptionItems);
    }

    if ($canCustomers) {
      $customerItems = [
        self::item('automation.customers.index', 'لیست مشتریان', 'users', 'automation.customers.*', exclude: ['automation.customers.create']),
      ];
      if ($canReceptionOps) {
        $customerItems[] = self::item('automation.customers.create', 'مشتری جدید', 'user-plus', 'automation.customers.create');
      }
      $sections[] = self::section('مشتریان', $customerItems);
    }

    if ($canInventory || $canProducts || $canAccounting) {
      $inventoryItems = [];
      if ($canInventory) {
        $inventoryItems[] = self::item('automation.inventory.index', 'موجودی قطعات', 'packages', 'automation.inventory.*', exclude: ['automation.inventory.reports.*']);
        if ($isAdmin || $user->canManageInventory()) {
          $inventoryItems[] = self::item('automation.inventory.reports.index', 'گزارش‌های انبار', 'report-analytics', 'automation.inventory.reports.*');
        }
      }
      if ($canProducts) {
        $inventoryItems[] = self::item('admin.products.index', 'محصولات فروشگاه', 'shopping-cart', 'admin.products.*');
      }
      if ($canAccounting) {
        $inventoryItems[] = self::item('automation.orders.index', 'سفارشات آنلاین', 'shopping-bag', 'automation.orders.*');
      }
      $sectionLabel = $canInventory ? 'انبار و فروشگاه' : 'فروشگاه';
      $sections[] = self::section($sectionLabel, $inventoryItems);
    }

    if ($canPos) {
      $sections[] = self::section('فروش حضوری', [
        self::item('automation.pos.index', 'ثبت فروش جدید', 'cash-register', 'automation.pos.index'),
        self::item('automation.pos.sales', 'مدیریت فروش‌ها', 'report-money', 'automation.pos.sales'),
      ]);
    }

    if ($canAccounting) {
      $financialItems = [
        self::item('automation.accounting.index', 'داشبورد مالی', 'chart-pie', 'automation.accounting.index'),
        self::item('automation.accounting.proforma.create', 'پیش‌فاکتور', 'file-invoice', 'automation.accounting.proforma.*'),
      ];
      $sections[] = self::section('مالی', $financialItems);
    }

    if ($isAdmin) {
      $systemItems = [];
      if ($isSuper) {
        $systemItems[] = self::item('super-admin.users.index', 'کاربران و نقش‌ها', 'users-group', 'super-admin.users.*');
      }
      $systemItems = array_merge($systemItems, [
        self::item('admin.sms.dashboard', 'پیامک', 'message-dots', 'admin.sms.*'),
        self::item('admin.activity-logs.index', 'لاگ فعالیت', 'history', 'admin.activity-logs.*'),
        self::item('admin.recycle-bin.index', 'سطل زباله', 'trash-x', 'admin.recycle-bin.*'),
        self::item('admin.settings.index', 'تنظیمات', 'settings', 'admin.settings.*'),
      ]);
      $sections[] = self::section('مدیریت سیستم', $systemItems);
    }

    $footerItems = [];
    // فقط مشتری خالص — کارمندان (حتی با رکورد customer) به my-account هدایت می‌شوند و خطا می‌گیرند
    if (! $user->isEmployee() && ($user->isCustomer() || $user->customer()->exists())) {
      $footerItems[] = self::item('customer.dashboard', 'پنل مشتری من', 'smart-home', 'customer.*');
    }
    $footerItems[] = self::item('home', 'سایت فروشگاه', 'world', 'home');
    $footerItems[] = [
      'href' => route('logout'),
      'label' => 'خروج',
      'icon' => 'logout-2',
      'active' => false,
      'class' => 'text-rose-400 hover:bg-rose-500/10 mt-1',
      'confirm' => 'آیا از خروج مطمئن هستید؟',
    ];
    $sections[] = self::section('', $footerItems, footer: true);

    return $sections;
  }

  /**
   * @param  list<array<string, mixed>>  $items
   * @return array{label: string, items: list<array<string, mixed>>, footer?: bool}
   */
  private static function section(string $label, array $items, bool $footer = false): array
  {
    return ['label' => $label, 'items' => $items, 'footer' => $footer];
  }

  /**
   * @param  list<string>  $exclude
   * @param  array<string, string|int>  $query
   */
  private static function item(
    string $routeName,
    string $label,
    string $icon,
    string $activePattern,
    array $exclude = [],
    array $query = [],
    ?\Closure $activeWhen = null,
  ): array {
    return [
      'href' => route($routeName, $query),
      'label' => $label,
      'icon' => $icon,
      'active' => $activeWhen ? $activeWhen() : self::routeActive($activePattern, $exclude),
    ];
  }

  /**
   * @param  list<string>  $exclude
   */
  private static function routeActive(string $pattern, array $exclude = []): bool
  {
    if (! Request::routeIs($pattern)) {
      return false;
    }
    foreach ($exclude as $ex) {
      if (Request::routeIs($ex)) {
        return false;
      }
    }

    return true;
  }
}
