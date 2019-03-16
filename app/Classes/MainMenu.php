<?php

namespace App\Classes;

/**
 * A simple class which Main Menu
 */
class MainMenu
{
    public function member($url, $id) {
        $menu = new Menu($url);
        $menu->add(new MenuItem(['title' => 'หน้าหลัก', 'icon' => 'fa-home', 'url' => "/member/$id"]));
        $menu->add(new MenuItem(['title' => 'ทุนเรือนหุ้น', 'icon' => 'fa-money', 'url' => "/member/$id/shareholding"]));
        $menu->add(new MenuItem(['title' => 'การกู้ยืม', 'icon' => 'fa-credit-card', 'url' => "/member/$id/loan"]));
        $menu->add(new MenuItem(['title' => 'การค้ำประกัน', 'icon' => 'fa-share-alt', 'url' => "/member/$id/guaruntee"]));
        $menu->add(new MenuItem(['title' => 'เงินปันผล/เฉลี่ยคืน', 'icon' => 'fa-dollar', 'url' => "/member/$id/dividend"]));

        return $menu->display();
    }

    public function admin($url, $role_id) {
        $super = $this->isSuper($role_id);
        $admin = $this->isAdmin($role_id);
        $viewer = $this->isViewer($role_id);

        $menu = new Menu($url);
        $menu->add(new MenuItem(['title' => 'หน้าหลัก', 'icon' => 'fa-home', 'url' => '/']));

        if ($super || $admin || $viewer) {
            $service = new MenuTree(['title' => 'บริการสมาชิก', 'icon' => 'fa-tag', 'url' => '/service']);
            $service->add(new MenuItem(['title' => 'สมาชิกสหกรณ์', 'icon' => 'fa-users', 'url' => '/service/member']));
            $service->add(new MenuItem(['title' => 'ทุนเรือนหุ้น', 'icon' => 'fa-money', 'url' => '/service/shareholding/member']));
            $service->add(new MenuItem(['title' => 'การกู้ยืม', 'icon' => 'fa-credit-card', 'url' => '/service/loan/member']));
            $service->add(new MenuItem(['title' => 'การค้ำประกัน', 'icon' => 'fa-share-alt', 'url' => '/service/guaruntee/member']));
            $service->add(new MenuItem(['title' => 'เงินปันผล/เฉลี่ยคืน', 'icon' => 'fa-heart-o', 'url' => '/service/dividend/member']));
            $menu->add($service);
        }
        
        if ($super || $admin) {
            $coop = new MenuTree(['title' => 'เครื่องมือช่วย', 'icon' => 'fa-briefcase', 'url' => '/coop']);
            $coop->add(new MenuItem(['title' => 'สัญญาเงินกู้', 'icon' => 'fa-usd', 'url' => '/coop/loanlist']));
            $coop->add(new MenuItem(['title' => 'ชำระค่าหุ้นปกติ', 'icon' => 'fa-eur', 'url' => '/coop/routine/shareholding']));
            $coop->add(new MenuItem(['title' => 'ชำระเงินกู้ปกติ', 'icon' => 'fa-gbp', 'url' => '/coop/routine/payment']));
            $coop->add(new MenuItem(['title' => 'ความสามารถในการกู้', 'icon' => 'fa-jpy', 'url' => '/coop/available/loan']));
            $coop->add(new MenuItem(['title' => 'ความสามารถในการค้ำ', 'icon' => 'fa-btc', 'url' => '/coop/available/bailsman']));
            $menu->add($coop);
        }

        if ($super || $admin) {
            $website = new MenuTree(['title' => 'จัดการเว็บไซต์', 'icon' => 'fa-tasks', 'url' => '/website']);
            $website->add(new MenuItem(['title' => 'เอกสาร/แบบฟอร์ม', 'icon' => 'fa-files-o', 'url' => '/website/documents']));
            $website->add(new MenuItem(['title' => 'ข่าวประชาสัมพันธ์', 'icon' => 'fa-rss', 'url' => '/website/carousels']));
            $website->add(new MenuItem(['title' => 'ข่าวสารสำหรับสมาชิก', 'icon' => 'fa-newspaper-o', 'url' => '/website/news']));
            $website->add(new MenuItem(['title' => 'สาระน่ารู้เกี่ยวกับสหกรณ์', 'icon' => 'fa-commenting', 'url' => '/website/knowledge']));
            $menu->add($website);
        }

        if ($super || $admin) {
            $database = new MenuTree(['title' => 'จัดการฐานข้อมูล', 'icon' => 'fa-database', 'url' => '/database']);
            $database->add(new MenuItem(['title' => 'ประเภทเงินกู้', 'icon' => 'fa-credit-card', 'url' => '/database/loantype']));
            $database->add(new MenuItem(['title' => 'เงื่อนไขการค้ำประกัน', 'icon' => 'fa-id-card-o', 'url' => '/database/bailsman']));
            $database->add(new MenuItem(['title' => 'อัตราเงินปันผล/เฉลี่ยคืน', 'icon' => 'fa-heart', 'url' => '/database/dividend']));
            $database->add(new MenuItem(['title' => 'รูปแบบใบรับเงิน', 'icon' => 'fa-file-text-o', 'url' => '/database/billing']));
            $menu->add($database);
        }

        if ($super || $admin || $viewer) {
            $officer = new MenuTree(['title' => 'ผู้ดูแลระบบ', 'icon' => 'fa-gears', 'url' => '/admin']);
            if ($super) $officer->add(new MenuItem(['title' => 'บัญชีเจ้าหน้าที่สหกรณ์', 'icon' => 'fa-user-circle-o', 'url' => '/admin/administrator']));
            if ($super || $admin) $officer->add(new MenuItem(['title' => 'บัญชีคณะกรรมการ', 'icon' => 'fa-user-circle-o', 'url' => '/admin/board']));
            if ($super || $admin) $officer->add(new MenuItem(['title' => 'บัญชีผู้ใช้งานระบบ', 'icon' => 'fa-user-circle-o', 'url' => '/admin/account']));
            if ($super || $admin) $officer->add(new MenuItem(['title' => 'จับรางวัล', 'icon' => 'fa-smile-o', 'url' => '/admin/reward']));
            $officer->add(new MenuItem(['title' => 'รายงานต่างๆ', 'icon' => 'fa-file-excel-o', 'url' => '/admin/report']));
            $officer->add(new MenuItem(['title' => 'สถิติการเข้าใช้งาน', 'icon' => 'fa-bar-chart', 'url' => '/admin/statistic']));
            $menu->add($officer);
        }

        return $menu->display();
    }

    private function isSuper($role_id) {
        return $role_id == 1;
    }

    private function isAdmin($role_id) {
        return $role_id == 2;
    }
    
    private function isViewer($role_id) {
        return $role_id == 3;
    }
}

/**
 * A simple class which Menu
 */
class Menu
{
    protected $items;
    protected $url;

    public function __construct($url) {
        $this->items = [];
        $this->url = $url;
    }

    // add items
    public function add($item) {
        $this->items[] = $item;
    }

    // display
    public function display() {
        $menu = '';

        foreach ($this->items as $item) {
            $menu .= $item->display($this->url);
        }

        return $menu;
    }
}

/**
 * A simple class which Menu Item
 */
class MenuItem extends Properties
{
    // method construct
    public function __construct(Array $array) {
        foreach($array as $key => $value) {
            $this->$key = $value;
        }
    }

    // title
    protected $title = 'Menu item';

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    // icon
    protected $icon = 'fa-circle-o';

    public function getIcon() {
        return $this->icon;
    }

    public function setIcon($icon) {
        $this->icon = $icon;
    }

    // url
    protected $url = '/';

    public function getUrl() {
        return $this->url;
    }

    public function setUrl($url) {
        $this->url = $url;
    }

    // push
    protected $push = null;

    public function getPush() {
        return $this->push;
    }

    public function setPush(MenuPush $push) {
        $this->push = $push;
    }

    // display
    public function display($url) {
        $menu = ($url == url($this->url)) ? '<li class="active">' : '<li>';
        $menu .= '<a href="' . url($this->url) . '">';
        $menu .= '<i class="fa ' . $this->icon . '"></i> <span>' . $this->title . '</span>';

        if (!empty($this->push)) {
            $menu .= '<small class="label pull-right ' . $this->push->background . '">' . $this->push->value . '</small>';
        }

        $menu .= '</a>';
        $menu .= '</li>';

        return $menu;
    }
}

/**
 * A simple class which Menu Push
 */
class MenuPush extends Properties
{
    // method construct
    public function __construct(Array $array) {
        foreach($array as $key => $value) {
            $this->$key = $value;
        }
    }

    // background
    protected $background = 'bg-red';

    public function getBackground() {
        return $this->background;
    }

    public function setBackground($background) {
        $this->background = $background;
    }

    // value
    protected $value = 0;

    public function getValue() {
        return $this->value;
    }

    public function setValue($value) {
        $this->value = $value;
    }
}

/**
 * A simple class which Menu Tree
 */
class MenuTree extends Properties
{
    protected $items = [];

    // method construct
    public function __construct(Array $array) {
        foreach($array as $key => $value) {
            $this->$key = $value;
        }
    }

    // title
    protected $title = 'Menu item';

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    // icon
    protected $icon = 'fa-circle-o';

    public function getIcon() {
        return $this->icon;
    }

    public function setIcon($icon) {
        $this->icon = $icon;
    }

    // url
    protected $url = '/';

    public function getUrl() {
        return $this->url;
    }

    public function setUrl($url) {
        $this->url = $url;
    }

    // add items
    public function add($item) {
        $this->items[] = $item;
    }

    // display
    public function display($url) {
        $menu = (str_contains($url, url($this->url))) ? '<li class="treeview active">' : '<li class="treeview">';
        $menu .= '<a href="#">';
        $menu .= '<i class="fa ' . $this->icon . '"></i> <span>' . $this->title . '</span>';
        $menu .= '<i class="fa fa-angle-left pull-right"></i>';
        $menu .= '</a>';
        $menu .= '<ul class="treeview-menu">';

        foreach($this->items as $item) {
            $menu .= $item->display($url);
        }

        $menu .= '</ul>';
        $menu .= '</li>';

        return $menu;
    }
}