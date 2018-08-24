<?php


class Breadcrumbs
{

    public function build_breadcrumbs()
    {

        $CI = &get_instance();
        $id = $CI->session->userdata('menu_active_id');
        $breadcrumbs = "";
        if (!empty($id)) {
            $menu_id = array_reverse($id);

            foreach ($menu_id as $v_id) {
                $menu = $CI->db->where('menu_id', $v_id)->get('tbl_menu')->result();
                foreach ($menu as $v_menu) {
                    $breadcrumbs = "<a class='text-muted' href='" . base_url() . $v_menu->link . "'>" . lang($v_menu->label) . "</a>\n";
                }
            }
        }
        return $breadcrumbs;
    }

}