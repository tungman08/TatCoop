<?php

namespace App\Classes;

/**
 * A simple class which Main Menu
 */
class MainMenu
{
    public function member($url) {
        $menu = new Menu($url);
        $menu->add(new MenuItem(['title' => 'หน้าหลัก', 'icon' => 'fa-home', 'url' => '/member']));
        $menu->add(new MenuItem(['title' => 'ทุนเรือนหุ้น', 'icon' => 'fa-money', 'url' => '/member/shareholding']));
        $menu->add(new MenuItem(['title' => 'การกู้ยืม', 'icon' => 'fa-credit-card', 'url' => '/member/loan']));
        $menu->add(new MenuItem(['title' => 'เงินปันผล', 'icon' => 'fa-dollar', 'url' => '/member/dividend']));
        $menu->add(new MenuItem(['title' => 'การค้ำประกัน', 'icon' => 'fa-share-alt', 'url' => '/member/guaruntee']));

        return $menu->display();
    }

    public function admin($super, $url) {
        $menu = new Menu($url);
        $menu->add(new MenuItem(['title' => 'หน้าหลัก', 'icon' => 'fa-home', 'url' => '/']));

        $website = new MenuTree(['title' => 'เว็บไซต์', 'icon' => 'fa-tasks', 'url' => '/website']);
        $website->add(new MenuItem(['title' => 'เอกสาร/แบบฟอร์ม', 'icon' => 'fa-files-o', 'url' => '/website/documents']));
        $website->add(new MenuItem(['title' => 'ข่าวประชาสัมพันธ์', 'icon' => 'fa-rss', 'url' => '/website/carousels']));
        $website->add(new MenuItem(['title' => 'ข่าวสารสำหรับสมาชิก', 'icon' => 'fa-newspaper-o', 'url' => '/website/news']));
        $website->add(new MenuItem(['title' => 'สาระน่ารู้', 'icon' => 'fa-commenting', 'url' => '/website/knowledge']));
        $menu->add($website);

        $service = new MenuTree(['title' => 'บริการสมาชิกสหกรณ์ฯ', 'icon' => 'fa-tag', 'url' => '/service']);
        $service->add(new MenuItem(['title' => 'จัดการสมาชิกสหกรณ์ฯ', 'icon' => 'fa-users', 'url' => '/service/member']));
        $service->add(new MenuItem(['title' => 'ทุนเรือนหุ้น', 'icon' => 'fa-money', 'url' => '/service/shareholding/member']));
        $service->add(new MenuItem(['title' => 'การกู้ยืม', 'icon' => 'fa-credit-card', 'url' => '/service/loan/member']));
        $service->add(new MenuItem(['title' => 'เงินปันผล', 'icon' => 'fa-dollar', 'url' => '/service/dividend/member']));
        $service->add(new MenuItem(['title' => 'การค้ำประกัน', 'icon' => 'fa-share-alt', 'url' => '/service/guaruntee/member']));
        $menu->add($service);

        $admin = new MenuTree(['title' => 'ผู้ดูแลระบบ', 'icon' => 'fa-gears', 'url' => '/admin']);
        if ($super) $admin->add(new MenuItem(['title' => 'จัดการบัญชีผู้ดูแลระบบฯ', 'icon' => 'fa-user-circle-o', 'url' => '/admin/administrator']));
        $admin->add(new MenuItem(['title' => 'จัดการบัญชีผู้ใช้งานระบบฯ', 'icon' => 'fa-user-circle-o', 'url' => '/admin/account']));
        $admin->add(new MenuItem(['title' => 'จัดการประเภทเงินกู้', 'icon' => 'fa-credit-card', 'url' => '/admin/loantype']));
        $admin->add(new MenuItem(['title' => 'จัดการอัตราเงินปันผล', 'icon' => 'fa-dollar', 'url' => '/admin/dividend']));
        $admin->add(new MenuItem(['title' => 'จัดการใบรับเงินค่าหุ้น', 'icon' => 'fa-file-text-o', 'url' => '/admin/billing']));
        $admin->add(new MenuItem(['title' => 'สถิติการเข้าใช้งาน', 'icon' => 'fa-bar-chart', 'url' => '/admin/statistic']));
        $menu->add($admin);

        return $menu->display();
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
        $menu = (url($url) == url($this->url)) ? '<li class="active">' : '<li>';
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
        $menu = (str_contains(url($url), url($this->url))) ? '<li class="treeview active">' : '<li class="treeview">';
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