<?php 
namespace Meigee\Blacknwhite\Model;

class cssGenerate
{
	
	public function __construct(
		\Meigee\Blacknwhite\Block\Frontend\CustomDesign $customDesign
    ) {
		$this->_customDesign = $customDesign;
    }

	public function getDesignValues($sectionId) {
		
		$general_site_bg = $this->_customDesign->getConfigData($sectionId, 'base_colors', 'general_site_bg');
		$general_main_color = $this->_customDesign->getConfigData($sectionId, 'base_colors', 'general_main_color');
		$general_second_color = $this->_customDesign->getConfigData($sectionId, 'base_colors', 'general_second_color');

		$menu_block_bg = $this->_customDesign->getConfigData($sectionId, 'menu_colors', 'menu_block_bg');
		$menu_block_border = $this->_customDesign->getConfigData($sectionId, 'menu_colors', 'menu_block_border');
		$menu_link_color = $this->_customDesign->getConfigData($sectionId, 'menu_colors', 'menu_link_color_d');
		$menu_link_color_h = $this->_customDesign->getConfigData($sectionId, 'menu_colors', 'menu_link_color_h');
		$menu_link_color_a = $this->_customDesign->getConfigData($sectionId, 'menu_colors', 'menu_link_color_a');
		$menu_link_color_f = $this->_customDesign->getConfigData($sectionId, 'menu_colors', 'menu_link_color_f');
		$menu_link_bg = $this->_customDesign->getConfigData($sectionId, 'menu_colors', 'menu_link_bg_d');
		$menu_link_bg_h = $this->_customDesign->getConfigData($sectionId, 'menu_colors', 'menu_link_bg_h');
		$menu_link_bg_a = $this->_customDesign->getConfigData($sectionId, 'menu_colors', 'menu_link_bg_a');
		$menu_link_bg_f = $this->_customDesign->getConfigData($sectionId, 'menu_colors', 'menu_link_bg_f');

		$menu_dropdown_wrapper_color = $this->_customDesign->getConfigData($sectionId, 'menu_colors', 'menu_dropdown_wrapper_color');
		$menu_dropdown_top_text_color = $this->_customDesign->getConfigData($sectionId, 'menu_colors', 'menu_dropdown_top_text_color');
		$menu_dropdown_top_border = $this->_customDesign->getConfigData($sectionId, 'menu_colors', 'menu_dropdown_top_border');
		$menu_dropdown_top_icons = $this->_customDesign->getConfigData($sectionId, 'menu_colors', 'menu_dropdown_top_icons');
		$menu_dropdown_top_titles_color = $this->_customDesign->getConfigData($sectionId, 'menu_colors', 'menu_dropdown_top_titles_color');
		$menu_dropdown_subtitles_color = $this->_customDesign->getConfigData($sectionId, 'menu_colors', 'menu_dropdown_subtitles_color');
		$menu_dropdown_subtitles_bg = $this->_customDesign->getConfigData($sectionId, 'menu_colors', 'menu_dropdown_subtitles_bg');
		$menu_dropdown_links_color = $this->_customDesign->getConfigData($sectionId, 'menu_colors', 'menu_dropdown_links_color_d');
		$menu_dropdown_links_color_h = $this->_customDesign->getConfigData($sectionId, 'menu_colors', 'menu_dropdown_links_color_h');
		$menu_dropdown_links_color_a = $this->_customDesign->getConfigData($sectionId, 'menu_colors', 'menu_dropdown_links_color_a');
		$menu_dropdown_links_color_f = $this->_customDesign->getConfigData($sectionId, 'menu_colors', 'menu_dropdown_links_color_f');
		$menu_dropdown_links_bg = $this->_customDesign->getConfigData($sectionId, 'menu_colors', 'menu_dropdown_links_bg_d');
		$menu_dropdown_links_bg_h = $this->_customDesign->getConfigData($sectionId, 'menu_colors', 'menu_dropdown_links_bg_h');
		$menu_dropdown_links_bg_a = $this->_customDesign->getConfigData($sectionId, 'menu_colors', 'menu_dropdown_links_bg_a');
		$menu_dropdown_links_bg_f = $this->_customDesign->getConfigData($sectionId, 'menu_colors', 'menu_dropdown_links_bg_f');
		$menu_dropdown_bottom_text_color = $this->_customDesign->getConfigData($sectionId, 'menu_colors', 'menu_dropdown_bottom_text_color');
		$menu_dropdown_bottom_bg = $this->_customDesign->getConfigData($sectionId, 'menu_colors', 'menu_dropdown_bottom_bg');

		$header_cart_color = $this->_customDesign->getConfigData($sectionId, 'menu_colors', 'header_cart_color_d');
		$header_cart_color_h = $this->_customDesign->getConfigData($sectionId, 'menu_colors', 'header_cart_color_h');
		$header_cart_color_a = $this->_customDesign->getConfigData($sectionId, 'menu_colors', 'header_cart_color_a');
		$header_cart_color_f = $this->_customDesign->getConfigData($sectionId, 'menu_colors', 'header_cart_color_f');
		$header_cart_bg = $this->_customDesign->getConfigData($sectionId, 'menu_colors', 'header_cart_bg_d');
		$header_cart_bg_h = $this->_customDesign->getConfigData($sectionId, 'menu_colors', 'header_cart_bg_h');
		$header_cart_bg_a = $this->_customDesign->getConfigData($sectionId, 'menu_colors', 'header_cart_bg_a');
		$header_cart_bg_f = $this->_customDesign->getConfigData($sectionId, 'menu_colors', 'header_cart_bg_f');

		$header_text = $this->_customDesign->getConfigData($sectionId, 'header_design', 'header_text');
		$header_link = $this->_customDesign->getConfigData($sectionId, 'header_design', 'header_link_d');
		$header_link_h = $this->_customDesign->getConfigData($sectionId, 'header_design', 'header_link_h');
		$header_link_a = $this->_customDesign->getConfigData($sectionId, 'header_design', 'header_link_a');
		$header_link_f = $this->_customDesign->getConfigData($sectionId, 'header_design', 'header_link_f');
		$header_bg = $this->_customDesign->getConfigData($sectionId, 'header_design', 'header_bg');
		$header_search_switchers_bg = $this->_customDesign->getConfigData($sectionId, 'header_design', 'header_search_switchers_bg');
		$header_search_switchers_color = $this->_customDesign->getConfigData($sectionId, 'header_design', 'header_search_switchers_color');
		$header_search_switchers_border = $this->_customDesign->getConfigData($sectionId, 'header_design', 'header_search_switchers_border');
		$header_search_input_border = $this->_customDesign->getConfigData($sectionId, 'header_design', 'header_search_input_border');
		$header_search_button_bg = $this->_customDesign->getConfigData($sectionId, 'header_design', 'header_search_button_bg_d');
		$header_search_button_bg_h = $this->_customDesign->getConfigData($sectionId, 'header_design', 'header_search_button_bg_h');
		$header_search_button_bg_a = $this->_customDesign->getConfigData($sectionId, 'header_design', 'header_search_button_bg_a');
		$header_search_button_bg_f = $this->_customDesign->getConfigData($sectionId, 'header_design', 'header_search_button_bg_f');
		$header_search_button_color = $this->_customDesign->getConfigData($sectionId, 'header_design', 'header_search_button_color_d');
		$header_search_button_color_h = $this->_customDesign->getConfigData($sectionId, 'header_design', 'header_search_button_color_h');
		$header_search_button_color_a = $this->_customDesign->getConfigData($sectionId, 'header_design', 'header_search_button_color_a');
		$header_search_button_color_f = $this->_customDesign->getConfigData($sectionId, 'header_design', 'header_search_button_color_f');

		$header_account_link_color = $this->_customDesign->getConfigData($sectionId, 'header_design', 'header_account_link_color_d');
		$header_account_link_color_h = $this->_customDesign->getConfigData($sectionId, 'header_design', 'header_account_link_color_h');
		$header_account_link_color_a = $this->_customDesign->getConfigData($sectionId, 'header_design', 'header_account_link_color_a');
		$header_account_link_color_f = $this->_customDesign->getConfigData($sectionId, 'header_design', 'header_account_link_color_f');
		$header_account_link_bg = $this->_customDesign->getConfigData($sectionId, 'header_design', 'header_account_link_bg_d');
		$header_account_link_bg_h = $this->_customDesign->getConfigData($sectionId, 'header_design', 'header_account_link_bg_h');
		$header_account_link_bg_a = $this->_customDesign->getConfigData($sectionId, 'header_design', 'header_account_link_bg_a');
		$header_account_link_bg_f = $this->_customDesign->getConfigData($sectionId, 'header_design', 'header_account_link_bg_f');
		$header_account_sub_bg = $this->_customDesign->getConfigData($sectionId, 'header_design', 'header_account_sub_bg');
		$header_account_sub_link_color = $this->_customDesign->getConfigData($sectionId, 'header_design', 'header_account_sub_link_color_d');
		$header_account_sub_link_color_h = $this->_customDesign->getConfigData($sectionId, 'header_design', 'header_account_sub_link_color_h');
		$header_account_sub_link_color_a = $this->_customDesign->getConfigData($sectionId, 'header_design', 'header_account_sub_link_color_a');
		$header_account_sub_link_color_f = $this->_customDesign->getConfigData($sectionId, 'header_design', 'header_account_sub_link_color_f');
		$header_account_sub_link_bg = $this->_customDesign->getConfigData($sectionId, 'header_design', 'header_account_sub_link_bg_d');
		$header_account_sub_link_bg_h = $this->_customDesign->getConfigData($sectionId, 'header_design', 'header_account_sub_link_bg_h');
		$header_account_sub_link_bg_a = $this->_customDesign->getConfigData($sectionId, 'header_design', 'header_account_sub_link_bg_a');
		$header_account_sub_link_bg_f = $this->_customDesign->getConfigData($sectionId, 'header_design', 'header_account_sub_link_bg_f');

		$header_slider_title_1 = $this->_customDesign->getConfigData($sectionId, 'header_slider', 'header_slider_title_1');
		$header_slider_title_2 = $this->_customDesign->getConfigData($sectionId, 'header_slider', 'header_slider_title_2');
		$header_slider_title_3 = $this->_customDesign->getConfigData($sectionId, 'header_slider', 'header_slider_title_3');
		$header_slider_title_4 = $this->_customDesign->getConfigData($sectionId, 'header_slider', 'header_slider_title_4');
		$header_slider_arrows_bg = $this->_customDesign->getConfigData($sectionId, 'header_slider', 'header_slider_arrows_bg_d');
		$header_slider_arrows_bg_h = $this->_customDesign->getConfigData($sectionId, 'header_slider', 'header_slider_arrows_bg_h');
		$header_slider_arrows_bg_a = $this->_customDesign->getConfigData($sectionId, 'header_slider', 'header_slider_arrows_bg_a');
		$header_slider_arrows_bg_f = $this->_customDesign->getConfigData($sectionId, 'header_slider', 'header_slider_arrows_bg_f');
		$header_slider_arrows_color = $this->_customDesign->getConfigData($sectionId, 'header_slider', 'header_slider_arrows_color_d');
		$header_slider_arrows_color_h = $this->_customDesign->getConfigData($sectionId, 'header_slider', 'header_slider_arrows_color_h');
		$header_slider_arrows_color_a = $this->_customDesign->getConfigData($sectionId, 'header_slider', 'header_slider_arrows_color_a');
		$header_slider_arrows_color_f = $this->_customDesign->getConfigData($sectionId, 'header_slider', 'header_slider_arrows_color_f');
		$header_slider_btn_bg = $this->_customDesign->getConfigData($sectionId, 'header_slider', 'header_slider_btn_bg_d');
		$header_slider_btn_bg_h = $this->_customDesign->getConfigData($sectionId, 'header_slider', 'header_slider_btn_bg_h');
		$header_slider_btn_bg_a = $this->_customDesign->getConfigData($sectionId, 'header_slider', 'header_slider_btn_bg_a');
		$header_slider_btn_bg_f = $this->_customDesign->getConfigData($sectionId, 'header_slider', 'header_slider_btn_bg_f');
		$header_slider_btn_color = $this->_customDesign->getConfigData($sectionId, 'header_slider', 'header_slider_btn_color_d');
		$header_slider_btn_color_h = $this->_customDesign->getConfigData($sectionId, 'header_slider', 'header_slider_btn_color_h');
		$header_slider_btn_color_a = $this->_customDesign->getConfigData($sectionId, 'header_slider', 'header_slider_btn_color_a');
		$header_slider_btn_color_f = $this->_customDesign->getConfigData($sectionId, 'header_slider', 'header_slider_btn_color_f');

		$content_title_color = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_title_color');
		$content_title_border = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_title_border');
		$content_toolar_label = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_label');
		$content_toolar_select_bg = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_select_bg_d');
		$content_toolar_select_bg_h = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_select_bg_h');
		$content_toolar_select_bg_a = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_select_bg_a');
		$content_toolar_select_bg_f = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_select_bg_f');
		$content_toolar_select_border = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_select_border_d');
		$content_toolar_select_border_h = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_select_border_h');
		$content_toolar_select_border_a = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_select_border_a');
		$content_toolar_select_border_f = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_select_border_f');
		$content_toolar_select_color = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_select_color_d');
		$content_toolar_select_color_h = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_select_color_h');
		$content_toolar_select_color_a = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_select_color_a');
		$content_toolar_select_color_f = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_select_color_f');
		$content_toolar_select_dropdown_link_bg = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_select_dropdown_link_bg_d');
		$content_toolar_select_dropdown_link_bg_h = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_select_dropdown_link_bg_h');
		$content_toolar_select_dropdown_link_bg_a = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_select_dropdown_link_bg_a');
		$content_toolar_select_dropdown_link_bg_f = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_select_dropdown_link_bg_f');
		$content_toolar_select_dropdown_link_color = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_select_dropdown_link_color_d');
		$content_toolar_select_dropdown_link_color_h = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_select_dropdown_link_color_h');
		$content_toolar_select_dropdown_link_color_a = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_select_dropdown_link_color_a');
		$content_toolar_select_dropdown_link_color_f = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_select_dropdown_link_color_f');
		$content_toolar_grid_switcher_bg = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_grid_switcher_bg_d');
		$content_toolar_grid_switcher_bg_h = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_grid_switcher_bg_h');
		$content_toolar_grid_switcher_bg_a = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_grid_switcher_bg_a');
		$content_toolar_grid_switcher_bg_f = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_grid_switcher_bg_f');
		$content_toolar_grid_switcher_color = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_grid_switcher_color_d');
		$content_toolar_grid_switcher_color_h = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_grid_switcher_color_h');
		$content_toolar_grid_switcher_color_a = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_grid_switcher_color_a');
		$content_toolar_grid_switcher_color_f = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_grid_switcher_color_f');
		$content_toolar_grid_switcher_border = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_grid_switcher_border_d');
		$content_toolar_grid_switcher_border_h = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_grid_switcher_border_h');
		$content_toolar_grid_switcher_border_a = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_grid_switcher_border_a');
		$content_toolar_grid_switcher_border_f = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_grid_switcher_border_f');
		$content_toolar_desc_switcher_bg = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_desc_switcher_bg_d');
		$content_toolar_desc_switcher_bg_h = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_desc_switcher_bg_h');
		$content_toolar_desc_switcher_bg_a = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_desc_switcher_bg_a');
		$content_toolar_desc_switcher_bg_f = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_desc_switcher_bg_f');
		$content_toolar_desc_switcher_color = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_desc_switcher_color_d');
		$content_toolar_desc_switcher_color_h = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_desc_switcher_color_h');
		$content_toolar_desc_switcher_color_a = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_desc_switcher_color_a');
		$content_toolar_desc_switcher_color_f = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_desc_switcher_color_f');
		$content_toolar_sidebar_btn_bg = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_sidebar_btn_bg_d');
		$content_toolar_sidebar_btn_bg_h = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_sidebar_btn_bg_h');
		$content_toolar_sidebar_btn_bg_a = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_sidebar_btn_bg_a');
		$content_toolar_sidebar_btn_bg_f = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_sidebar_btn_bg_f');
		$content_toolar_sidebar_btn_border = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_sidebar_btn_border_d');
		$content_toolar_sidebar_btn_border_h = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_sidebar_btn_border_h');
		$content_toolar_sidebar_btn_border_a = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_sidebar_btn_border_a');
		$content_toolar_sidebar_btn_border_f = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_sidebar_btn_border_f');
		$content_toolar_sidebar_btn_color = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_sidebar_btn_color_d');
		$content_toolar_sidebar_btn_color_h = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_sidebar_btn_color_h');
		$content_toolar_sidebar_btn_color_a = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_sidebar_btn_color_a');
		$content_toolar_sidebar_btn_color_f = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_toolar_sidebar_btn_color_f');

		$content_pager_btns_divider = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_pager_btns_divider');
		$content_pager_btns_bg = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_pager_btns_bg_d');
		$content_pager_btns_bg_h = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_pager_btns_bg_h');
		$content_pager_btns_bg_a = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_pager_btns_bg_a');
		$content_pager_btns_bg_f = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_pager_btns_bg_f');
		$content_pager_btns_border = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_pager_btns_border_d');
		$content_pager_btns_border_h = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_pager_btns_border_h');
		$content_pager_btns_border_a = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_pager_btns_border_a');
		$content_pager_btns_border_f = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_pager_btns_border_f');
		$content_pager_btns_color = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_pager_btns_color_d');
		$content_pager_btns_color_h = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_pager_btns_color_h');
		$content_pager_btns_color_a = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_pager_btns_color_a');
		$content_pager_btns_color_f = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_pager_btns_color_f');
		
		$content_home_banners_title_color = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_home_banners_title_color');
		$content_home_banners_title2_color = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_home_banners_title2_color');
		$content_home_banners_subtitle_color = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_home_banners_subtitle_color');
		$content_home_banners_btn_bg = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_home_banners_btn_bg_d');
		$content_home_banners_btn_bg_h = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_home_banners_btn_bg_h');
		$content_home_banners_btn_bg_a = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_home_banners_btn_bg_a');
		$content_home_banners_btn_bg_f = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_home_banners_btn_bg_f');
		$content_home_banners_btn_color = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_home_banners_btn_color_d');
		$content_home_banners_btn_color_h = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_home_banners_btn_color_h');
		$content_home_banners_btn_color_a = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_home_banners_btn_color_a');
		$content_home_banners_btn_color_f = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_home_banners_btn_color_f');
		$content_sidebar_features_icon_color = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_sidebar_features_icon_color');
		$content_sidebar_features_icon_bg = $this->_customDesign->getConfigData($sectionId, 'content_design', 'content_sidebar_features_icon_bg');

		$not_found_search_bg = $this->_customDesign->getConfigData($sectionId, 'not_found_page_design', 'not_found_search_bg');
		$not_found_search_icon = $this->_customDesign->getConfigData($sectionId, 'not_found_page_design', 'not_found_search_icon');
		$not_found_search_border = $this->_customDesign->getConfigData($sectionId, 'not_found_page_design', 'not_found_search_border');
		$not_found_search_color = $this->_customDesign->getConfigData($sectionId, 'not_found_page_design', 'not_found_search_color');
		$not_found_title_color = $this->_customDesign->getConfigData($sectionId, 'not_found_page_design', 'not_found_title_color');
		$not_found_subtitle_color = $this->_customDesign->getConfigData($sectionId, 'not_found_page_design', 'not_found_subtitle_color');
		$not_found_text_color = $this->_customDesign->getConfigData($sectionId, 'not_found_page_design', 'not_found_text_color');
		$not_found_btn_border = $this->_customDesign->getConfigData($sectionId, 'not_found_page_design', 'not_found_btn_border_d');
		$not_found_btn_border_h = $this->_customDesign->getConfigData($sectionId, 'not_found_page_design', 'not_found_btn_border_h');
		$not_found_btn_border_a = $this->_customDesign->getConfigData($sectionId, 'not_found_page_design', 'not_found_btn_border_a');
		$not_found_btn_border_f = $this->_customDesign->getConfigData($sectionId, 'not_found_page_design', 'not_found_btn_border_f');
		$not_found_btn_color = $this->_customDesign->getConfigData($sectionId, 'not_found_page_design', 'not_found_btn_color_d');
		$not_found_btn_color_h = $this->_customDesign->getConfigData($sectionId, 'not_found_page_design', 'not_found_btn_color_h');
		$not_found_btn_color_a = $this->_customDesign->getConfigData($sectionId, 'not_found_page_design', 'not_found_btn_color_a');
		$not_found_btn_color_f = $this->_customDesign->getConfigData($sectionId, 'not_found_page_design', 'not_found_btn_color_f');
		$not_found_btn_bg = $this->_customDesign->getConfigData($sectionId, 'not_found_page_design', 'not_found_btn_bg_d');
		$not_found_btn_bg_h = $this->_customDesign->getConfigData($sectionId, 'not_found_page_design', 'not_found_btn_bg_h');
		$not_found_btn_bg_a = $this->_customDesign->getConfigData($sectionId, 'not_found_page_design', 'not_found_btn_bg_a');
		$not_found_btn_bg_f = $this->_customDesign->getConfigData($sectionId, 'not_found_page_design', 'not_found_btn_bg_f');
		$not_found_footer_links_color = $this->_customDesign->getConfigData($sectionId, 'not_found_page_design', 'not_found_footer_links_color_d');
		$not_found_footer_links_color_h = $this->_customDesign->getConfigData($sectionId, 'not_found_page_design', 'not_found_footer_links_color_h');
		$not_found_footer_links_color_a = $this->_customDesign->getConfigData($sectionId, 'not_found_page_design', 'not_found_footer_links_color_a');
		$not_found_footer_links_color_f = $this->_customDesign->getConfigData($sectionId, 'not_found_page_design', 'not_found_footer_links_color_f');
		$not_found_copyright_color = $this->_customDesign->getConfigData($sectionId, 'not_found_page_design', 'not_found_copyright_color');
		$not_found_copyright_link_color = $this->_customDesign->getConfigData($sectionId, 'not_found_page_design', 'not_found_copyright_link_color_d');
		$not_found_copyright_link_color_h = $this->_customDesign->getConfigData($sectionId, 'not_found_page_design', 'not_found_copyright_link_color_h');
		$not_found_copyright_link_color_a = $this->_customDesign->getConfigData($sectionId, 'not_found_page_design', 'not_found_copyright_link_color_a');
		$not_found_copyright_link_color_f = $this->_customDesign->getConfigData($sectionId, 'not_found_page_design', 'not_found_copyright_link_color_f');

		$buttons_type1_bg = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_type1_bg_d');
		$buttons_type1_bg_h = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_type1_bg_h');
		$buttons_type1_bg_a = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_type1_bg_a');
		$buttons_type1_bg_f = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_type1_bg_f');
		$buttons_type1_border = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_type1_border_d');
		$buttons_type1_border_h = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_type1_border_h');
		$buttons_type1_border_a = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_type1_border_a');
		$buttons_type1_border_f = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_type1_border_f');
		$buttons_type1_color = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_type1_color_d');
		$buttons_type1_color_h = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_type1_color_h');
		$buttons_type1_color_a = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_type1_color_a');
		$buttons_type1_color_f = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_type1_color_f');

		$buttons_type2_bg = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_type2_bg_d');
		$buttons_type2_bg_h = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_type2_bg_h');
		$buttons_type2_bg_a = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_type2_bg_a');
		$buttons_type2_bg_f = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_type2_bg_f');
		$buttons_type2_border = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_type2_border_d');
		$buttons_type2_border_h = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_type2_border_h');
		$buttons_type2_border_a = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_type2_border_a');
		$buttons_type2_border_f = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_type2_border_f');
		$buttons_type2_color = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_type2_color_d');
		$buttons_type2_color_h = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_type2_color_h');
		$buttons_type2_color_a = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_type2_color_a');
		$buttons_type2_color_f = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_type2_color_f');

		$buttons_productpage_cart_btn_bg = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_productpage_cart_btn_bg_d');
		$buttons_productpage_cart_btn_bg_h = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_productpage_cart_btn_bg_h');
		$buttons_productpage_cart_btn_bg_a = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_productpage_cart_btn_bg_a');
		$buttons_productpage_cart_btn_bg_f = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_productpage_cart_btn_bg_f');
		$buttons_productpage_cart_btn_border = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_productpage_cart_btn_border_d');
		$buttons_productpage_cart_btn_border_h = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_productpage_cart_btn_border_h');
		$buttons_productpage_cart_btn_border_a = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_productpage_cart_btn_border_a');
		$buttons_productpage_cart_btn_border_f = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_productpage_cart_btn_border_f');
		$buttons_productpage_cart_btn_color = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_productpage_cart_btn_color_d');
		$buttons_productpage_cart_btn_color_h = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_productpage_cart_btn_color_h');
		$buttons_productpage_cart_btn_color_a = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_productpage_cart_btn_color_a');
		$buttons_productpage_cart_btn_color_f = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_productpage_cart_btn_color_f');

		$buttons_quickview_btn_bg = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_quickview_btn_bg_d');
		$buttons_quickview_btn_bg_h = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_quickview_btn_bg_h');
		$buttons_quickview_btn_bg_a = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_quickview_btn_bg_a');
		$buttons_quickview_btn_bg_f = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_quickview_btn_bg_f');
		$buttons_quickview_btn_color = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_quickview_btn_color_d');
		$buttons_quickview_btn_color_h = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_quickview_btn_color_h');
		$buttons_quickview_btn_color_a = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_quickview_btn_color_a');
		$buttons_quickview_btn_color_f = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_quickview_btn_color_f');

		$buttons_moreviews_btn_bg = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_moreviews_btn_bg_d');
		$buttons_moreviews_btn_bg_h = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_moreviews_btn_bg_h');
		$buttons_moreviews_btn_bg_a = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_moreviews_btn_bg_a');
		$buttons_moreviews_btn_bg_f = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_moreviews_btn_bg_f');
		$buttons_moreviews_btn_color = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_moreviews_btn_color_d');
		$buttons_moreviews_btn_color_h = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_moreviews_btn_color_h');
		$buttons_moreviews_btn_color_a = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_moreviews_btn_color_a');
		$buttons_moreviews_btn_color_f = $this->_customDesign->getConfigData($sectionId, 'buttons_design', 'buttons_moreviews_btn_color_f');

		$products_bg = $this->_customDesign->getConfigData($sectionId, 'products_design', 'products_bg');
		$products_border = $this->_customDesign->getConfigData($sectionId, 'products_design', 'products_border');
		$products_title_color = $this->_customDesign->getConfigData($sectionId, 'products_design', 'products_title_color_d');
		$products_title_color_h = $this->_customDesign->getConfigData($sectionId, 'products_design', 'products_title_color_h');
		$products_title_color_a = $this->_customDesign->getConfigData($sectionId, 'products_design', 'products_title_color_a');
		$products_title_color_f = $this->_customDesign->getConfigData($sectionId, 'products_design', 'products_title_color_f');
		$products_text_color = $this->_customDesign->getConfigData($sectionId, 'products_design', 'products_text_color');
		$products_link_color = $this->_customDesign->getConfigData($sectionId, 'products_design', 'products_link_color_d');
		$products_link_color_h = $this->_customDesign->getConfigData($sectionId, 'products_design', 'products_link_color_h');
		$products_link_color_a = $this->_customDesign->getConfigData($sectionId, 'products_design', 'products_link_color_a');
		$products_link_color_f = $this->_customDesign->getConfigData($sectionId, 'products_design', 'products_link_color_f');
		$products_price_color = $this->_customDesign->getConfigData($sectionId, 'products_design', 'products_price_color');
		$products_old_price_color = $this->_customDesign->getConfigData($sectionId, 'products_design', 'products_old_price_color');
		$products_special_price_color = $this->_customDesign->getConfigData($sectionId, 'products_design', 'products_special_price_color');
		$products_divider_color = $this->_customDesign->getConfigData($sectionId, 'products_design', 'products_divider_color');

		$products_labels_sale_bg = $this->_customDesign->getConfigData($sectionId, 'products_design', 'products_labels_sale_bg');
		$products_labels_sale_color = $this->_customDesign->getConfigData($sectionId, 'products_design', 'products_labels_sale_color');
		$products_labels_new_bg = $this->_customDesign->getConfigData($sectionId, 'products_design', 'products_labels_new_bg');
		$products_labels_new_color = $this->_customDesign->getConfigData($sectionId, 'products_design', 'products_labels_new_color');

		$price_countdown_timer_bg = $this->_customDesign->getConfigData($sectionId, 'price_countdown', 'price_countdown_timer_bg');
		$price_countdown_timer_color = $this->_customDesign->getConfigData($sectionId, 'price_countdown', 'price_countdown_timer_color');

		$social_links_bg = $this->_customDesign->getConfigData($sectionId, 'social_links', 'social_links_bg_d');
		$social_links_bg_h = $this->_customDesign->getConfigData($sectionId, 'social_links', 'social_links_bg_h');
		$social_links_bg_a = $this->_customDesign->getConfigData($sectionId, 'social_links', 'social_links_bg_a');
		$social_links_bg_f = $this->_customDesign->getConfigData($sectionId, 'social_links', 'social_links_bg_f');
		$social_links_border = $this->_customDesign->getConfigData($sectionId, 'social_links', 'social_links_border_d');
		$social_links_border_h = $this->_customDesign->getConfigData($sectionId, 'social_links', 'social_links_border_h');
		$social_links_border_a = $this->_customDesign->getConfigData($sectionId, 'social_links', 'social_links_border_a');
		$social_links_border_f = $this->_customDesign->getConfigData($sectionId, 'social_links', 'social_links_border_f');
		$social_links_color = $this->_customDesign->getConfigData($sectionId, 'social_links', 'social_links_color_d');
		$social_links_color_h = $this->_customDesign->getConfigData($sectionId, 'social_links', 'social_links_color_h');
		$social_links_color_a = $this->_customDesign->getConfigData($sectionId, 'social_links', 'social_links_color_a');
		$social_links_color_f = $this->_customDesign->getConfigData($sectionId, 'social_links', 'social_links_color_f');

		$footer_top_title_color = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_top_title_color');
		$footer_top_title_border = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_top_title_border');
		$footer_top_subtitle_color = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_top_subtitle_color');
		$footer_top_bg = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_top_bg');
		$footer_top_text_color = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_top_text_color');
		$footer_top_link_color = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_top_link_color_d');
		$footer_top_link_color_h = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_top_link_color_h');
		$footer_top_link_color_a = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_top_link_color_a');
		$footer_top_link_color_f = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_top_link_color_f');
		$footer_top_link_bg = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_top_link_bg_d');
		$footer_top_link_bg_h = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_top_link_bg_h');
		$footer_top_link_bg_a = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_top_link_bg_a');
		$footer_top_link_bg_f = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_top_link_bg_f');
		$footer_top_icon_block_bg = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_top_icon_block_bg_d');
		$footer_top_icon_block_bg_h = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_top_icon_block_bg_h');
		$footer_top_icon_block_bg_a = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_top_icon_block_bg_a');
		$footer_top_icon_block_bg_f = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_top_icon_block_bg_f');
		$footer_top_icon_block_color = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_top_icon_block_color_d');
		$footer_top_icon_block_color_h = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_top_icon_block_color_h');
		$footer_top_icon_block_color_a = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_top_icon_block_color_a');
		$footer_top_icon_block_color_f = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_top_icon_block_color_f');
		$footer_top_icon_block_border = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_top_icon_block_border');

		$footer_middle_title_color = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_middle_title_color');
		$footer_middle_title_border_color = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_middle_title_border_color');
		$footer_middle_bg = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_middle_bg');
		$footer_middle_text_color = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_middle_text_color');
		$footer_middle_link_color = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_middle_link_color_d');
		$footer_middle_link_color_h = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_middle_link_color_h');
		$footer_middle_link_color_a = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_middle_link_color_a');
		$footer_middle_link_color_f = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_middle_link_color_f');
		$footer_middle_link_bg = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_middle_link_bg_d');
		$footer_middle_link_bg_h = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_middle_link_bg_h');
		$footer_middle_link_bg_a = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_middle_link_bg_a');
		$footer_middle_link_bg_f = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_middle_link_bg_f');

		$footer_bottom_bg = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_bottom_bg');
		$footer_bottom_text_color = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_bottom_text_color');
		$footer_bottom_link_color = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_bottom_link_color_d');
		$footer_bottom_link_color_h = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_bottom_link_color_h');
		$footer_bottom_link_color_a = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_bottom_link_color_a');
		$footer_bottom_link_color_f = $this->_customDesign->getConfigData($sectionId, 'footer_design', 'footer_bottom_link_color_f');
			
$cssData = '
	/***** New Css styles *****/
	
	html body {background-color: '.$general_site_bg.';}
	body button.primary:hover,
	body .btn-primary.disabled:hover,
	body .btn-primary[disabled]:hover,
	body fieldset[disabled] .btn-primary:hover,
	body .btn-primary.disabled:focus,
	body .btn-primary[disabled]:focus,
	body fieldset[disabled] .btn-primary:focus,
	body .btn-primary.disabled.focus,
	body .btn-primary[disabled].focus,
	body fieldset[disabled] .btn-primary.focus,
	.actions-toolbar .primary .action:hover,
	.actions-toolbar .secondary .action:hover,
	body .btn:hover,
	body button:hover,
	.cart-container .cart.actions a.continue:hover,
	body .btn.btn-primary.type-2:hover,
	#popup-block .block.newsletter .content button.primary:hover,
	body button.primary.checkout,
	body .btn.btn-primary,
	ul.social-links li a:hover i {border-color: '.$general_main_color.'; background-color: '.$general_main_color.';}

	.owl-nav i:hover,
	.product-labels span.label-sale,
	#home-slider .owl-nav div:hover,
	.toolbar .toolbar-sorter .sorter-action span:hover,
	.toolbar .modes a:hover span,
	.toolbar .modes strong span,
	.cms-no-route .page-not-found .btn:hover,
	.ekko-lightbox-nav-overlay a i:hover,
	body .breadcrumb span:after,
	body .breadcrumbs > .items > li:before{background-color: '.$general_main_color.';}
	.customer-welcome .action.switch:after,
	body .breadcrumb a:hover + span,
	body .breadcrumbs > .items > li:hover:after,
	.products-grid .actions-secondary a:hover,
	#home-slider .item .slide-container.slide-skin-2 h3 span,
	#home-slider .item .slide-container.slide-skin-3 h3 span,
	.stock.unavailable span,
	.product-social-links .action.mailto:hover,
	.product-item-actions a.action:hover,
	.product-addto-links .action:hover,
	.product-info-main .product-info-price-inner .stock.unavailable span,
	.related .block-actions .action:hover,
	#reviews #review-form .review-legend strong,
	#product-review-container .review-author strong,
	body a.action.remind,
	body .actions-toolbar a.action.back,
	.login-container .block .action.remind,
	body a.action.remind:hover,
	body .actions-toolbar a.action.back:hover,
	.login-container .block .action.remind:hover,
	.account .content-inner a:hover,
	.box .box-title a i:hover,
	#my-orders-table a:hover,
	.sidebar .block li a.delete:hover,
	body .table .action i:hover,
	.checkout-methods-items li a:hover,
	.multicheckout .box-title.with-link .action:hover,
	.minicart-items .action.edit:hover:before,
	.minicart-items .action.delete:hover:before,
	.minicart-items .action.edit:active:before,
	.minicart-items .action.delete:active:before,
	.modal-open .modal.ekko-lightbox .modal-header .close:hover:before,
	.parallax-banners-wrapper .text-banner .banner-content.colors-2 h2 span,
	.parallax-banners-wrapper .text-banner .banner-content.colors-2 h5,
	.parallax-banners-wrapper .text-banner .banner-content.colors-3 h3 span,
	.price,
	.products-list .product-item-description a:hover,
	.products-grid .product-item-description a:hover,
	body .table .action i:hover,
	.customer-welcome .action.switch:after {color: '.$general_main_color.';}
	.label-type-4.two-items .label-sale:before,
	.label-type-4.two-items .label-sale:after,
	.label-type-4 .label-sale:after {
		border-bottom-color: '.$general_main_color.';
	}
	.label-type-4 .label-sale:before {
		border-top-color: '.$general_main_color.';
	}


	.parallax-banners-wrapper .text-banner .banner-content.colors-1 h3,
	.parallax-banners-wrapper .text-banner .banner-content.colors-3 h4,
	.parallax-banners-wrapper .text-banner .banner-content.colors-4 h3,
	#home-slider .item h3,
	#home-slider .item .slide-container.slide-skin-2 h4,
	#home-slider .item .slide-container.slide-skin-3 h4,
	.products-list .product-item-description a,
	.products-grid .product-item-description a {color: '.$general_second_color.';}

	.product-labels span {background-color: '.$general_second_color.';}
	.label-type-4 .label-new:before {
		border-top-color: '.$general_second_color.';
	}
	.label-type-4 .label-new:after {
		border-bottom-color: '.$general_second_color.';
	}
	.header-wrapper .menu-inner,
	.header-wrapper .page-header.header-3 .toggle-nav,
	body.wide-layout .page-header.header-4 .menu-wrapper,
	body.boxed-layout .page-header.header-4 .menu-wrapper > .container {
		background-color: '.$menu_block_bg.';
		border-top-color: '.$menu_block_border.';
		border-bottom-color: '.$menu_block_border.';
	}
	.boxed-layout .page-header.header-2 + #sticky-header .container,
	.wide-layout .page-header.header-2 + #sticky-header,
	.wide-layout .page-header.header-2 + #sticky-header .container {
		background-color: '.$menu_block_bg.';
	}
	.page-header .action.nav-toggle {
		background-color: '.$menu_link_bg.';
		color: '.$menu_link_color.';
	}
	@media only screen and (min-width: 1008px) {
		.header-wrapper .navbar-collapse.collapse a.level-top,
		.page-header.header-2 + #sticky-header .navbar-collapse.collapse a.level-top,
		.page-header.header-2 + #sticky-header .block-search .search-button {
			background-color: '.$menu_link_bg.';
			color: '.$menu_link_color.';
		}
		.header-wrapper .navbar-collapse.collapse a.level-top.ui-state-focus:hover,
		.page-header.header-2 + #sticky-header .navbar-collapse.collapse a.level-top.ui-state-focus:hover,
		.page-header.header-2 + #sticky-header .block-search .search-button:hover {
			background-color: '.$menu_link_bg_h.';
			color: '.$menu_link_color_h.';
		}
		.header-wrapper .navbar-collapse.collapse a.level-top.ui-state-focus,
		.header-wrapper .navbar-collapse.collapse li.active a.level-top,
		.page-header.header-2 + #sticky-header .navbar-collapse.collapse a.level-top.ui-state-focus,
		.page-header.header-2 + #sticky-header .navbar-collapse.collapse li.active a.level-top,
		.page-header.header-2 + #sticky-header .block-search .search-button:active {
			background-color: '.$menu_link_bg_a.';
			color: '.$menu_link_color_a.';
		}
		.header-wrapper .navbar-collapse.collapse a.level-top:focus,
		.page-header.header-2 + #sticky-header .navbar-collapse.collapse a.level-top:focus,
		.page-header.header-2 + #sticky-header .block-search .search-button:focus {
			background-color: '.$menu_link_bg_f.';
			color: '.$menu_link_color_f.';
		}
		.megamenu-wrapper,
		.navigation .level0 .submenu {background-color: '.$menu_dropdown_wrapper_color.';}
		.megamenu-wrapper .top-content {color: '.$menu_dropdown_top_text_color.';}
		.megamenu-wrapper .top-content .top-menu-links {border-color: '.$menu_dropdown_top_border.';}
		.megamenu-wrapper .top-content .top-menu-features li i {color: '.$menu_dropdown_top_icons.'; border-color: '.$menu_dropdown_top_icons.';}
		.megamenu-wrapper .top-content .top-menu-features li span h3 {color: '.$menu_dropdown_top_titles_color.';}
		#sticky-megamenu .megamenu-wrapper ul.level0:not(.default-menu) li.level1 > a,
		#megamenu .megamenu-wrapper ul.level0:not(.default-menu) li.level1 > a {color: '.$menu_dropdown_subtitles_color.'; background-color: '.$menu_dropdown_subtitles_bg.';}
		#sticky-megamenu .topmenu .megamenu-wrapper ul.level1 a,
		#megamenu .topmenu .megamenu-wrapper ul.level1 a,
		.navigation .level0 .submenu a {color: '.$menu_dropdown_links_color.'; background-color: '.$menu_dropdown_links_bg.';}
		#sticky-megamenu .topmenu .megamenu-wrapper ul.level1 a:hover,
		#megamenu .topmenu .megamenu-wrapper ul.level1 a:hover,
		.navigation .level0 .submenu a:hover {color: '.$menu_dropdown_links_color_h.'; background-color: '.$menu_dropdown_links_bg_h.';}
		#sticky-megamenu .topmenu .megamenu-wrapper ul.level1 a:active,
		#megamenu .topmenu .megamenu-wrapper ul.level1 a:active,
		.navigation .level0 .submenu a:active {color: '.$menu_dropdown_links_color_a.'; background-color: '.$menu_dropdown_links_bg_a.';}
		#sticky-megamenu .topmenu .megamenu-wrapper ul.level1 a:focus,
		#megamenu .topmenu .megamenu-wrapper ul.level1 a:focus,
		.navigation .level0 .submenu a:focus {color: '.$menu_dropdown_links_color_f.'; background-color: '.$menu_dropdown_links_bg_f.';}
		.megamenu-wrapper .bottom-content {background-color: '.$menu_dropdown_bottom_bg.'; color: '.$menu_dropdown_bottom_text_color.';}
	}
	
	.minicart-wrapper .action.showcart.title-cart {background-color: '.$header_cart_bg.'; color: '.$header_cart_color.';}
	.minicart-wrapper .title-cart .counter:not(.empty):before {background-color: '.$header_cart_color.';}
	.minicart-wrapper .action.showcart.title-cart:hover {background-color: '.$header_cart_bg_h.'; color: '.$header_cart_color_h.';}
	.minicart-wrapper .title-cart:hover .counter:not(.empty):before {background-color: '.$header_cart_color_h.';}
	.minicart-wrapper .action.showcart.title-cart.active {background-color: '.$header_cart_bg_a.'; color: '.$header_cart_color_a.';}
	.minicart-wrapper .title-cart.active .counter:not(.empty):before {background-color: '.$header_cart_color_a.';}
	.minicart-wrapper .action.showcart.title-cart:focus {background-color: '.$header_cart_bg_f.'; color: '.$header_cart_color_f.';}
	.minicart-wrapper .title-cart:focus .counter:not(.empty):before {background-color: '.$header_cart_color_a.';}
	
	.page-header,
	.page-header .welcome,
	.page-header.header-2 .welcome,
	.page-header .header-switcher ul li span {color: '.$header_text.';}
	.page-header .header-switcher + .header-switcher:before,
	.header.links li:first-child:before {border-color: '.$header_text.'; opacity: 0.7;}
	.page-header .header-switcher ul li a,
	.page-header a {color: '.$header_link.';}
	.page-header .header-switcher ul li a:hover,
	.page-header a:hover {color: '.$header_link_h.';}
	.page-header .header-switcher ul li a:active,
	.page-header a:active {color: '.$header_link_a.';}
	.page-header .header-switcher ul li a:focus,
	.page-header a:focus {color: '.$header_link_f.';}
	
	body.wide-layout .page-header,
	.boxed-layout .header-wrapper .page-header > .container,
	.boxed-layout .header-wrapper .menu-wrapper .container,
	.header-wrapper .page-header.header-3 .container {background-color: '.$header_bg.';}

	body.wide-layout .page-header .container {background-color: transparent;}
	.header-wrapper .page-header .block-search .input-group,
	.page-header .header-switcher .switcher-options,
	.header-wrapper .page-header.header-2 .block-search .input-group {
		background-color: '.$header_search_switchers_bg.';
		border-color: '.$header_search_switchers_border.';
	}
	.page-header .header-switcher .switcher-trigger span {color: '.$header_search_switchers_color.';}
	.page-header .header-switcher .options .action.toggle:after,
	.page-header .header-switcher .options .action.toggle:hover:after {color: '.$header_search_switchers_color.'; opacity: 0.7;}
	.header-wrapper .page-header .block-search .input-group input,
	.header-wrapper .page-header.header-2 .block-search .input-group input {
		color: '.$header_search_switchers_color.';
		border-color: '.$header_search_input_border.';
		background-color: transparent;
	}
	.header-wrapper .page-header .block-search .input-group input::-webkit-input-placeholder,
	.header-wrapper .page-header.header-2 .block-search .input-group input::-webkit-input-placeholder {
		color: '.$header_search_switchers_color.';
	}
	.header-wrapper .page-header .block-search .input-group input::-moz-placeholder,
	.header-wrapper .page-header.header-2 .block-search .input-group input::-moz-placeholder {
		color: '.$header_search_switchers_color.';
	}
	.header-wrapper .page-header .block-search .input-group input:-moz-placeholder,
	.header-wrapper .page-header.header-2 .block-search .input-group input:-moz-placeholder {
		color: '.$header_search_switchers_color.';
	}
	.header-wrapper .page-header .block-search .input-group input:-ms-input-placeholder,
	.header-wrapper .page-header.header-2 .block-search .input-group input:-ms-input-placeholder {
		color: '.$header_search_switchers_color.';
	}
	.header-wrapper .page-header .block-search .input-group .btn,
	.header-wrapper .page-header.header-2 .block-search .input-group .btn {
		background-color: '.$header_search_button_bg.';
		color: '.$header_search_button_color.';
	}
	.header-wrapper .page-header .block-search .input-group .btn:hover,
	.header-wrapper .page-header.header-2 .block-search .input-group .btn:hover {
		background-color: '.$header_search_button_bg_h.';
		color: '.$header_search_button_color_h.';
	}
	.header-wrapper .page-header .block-search .input-group .btn:active,
	.header-wrapper .page-header.header-2 .block-search .input-group .btn:active {
		background-color: '.$header_search_button_bg_a.';
		color: '.$header_search_button_color_a.';
	}
	.header-wrapper .page-header .block-search .input-group .btn:focus,
	.header-wrapper .page-header.header-2 .block-search .input-group .btn:focus {
		background-color: '.$header_search_button_bg_f.';
		color: '.$header_search_button_color_f.';
	}
	.header-wrapper .page-header .header.links li a,
	.header.links li .customer-name {
		color: '.$header_account_link_color.';
		background-color: '.$header_account_link_bg.';
	}
	.header-wrapper .page-header .header.links li a:hover,
	.header.links li .customer-name:hover {
		color: '.$header_account_link_color_h.';
		background-color: '.$header_account_link_bg_h.';
	}
	.header-wrapper .page-header .header.links li a:active,
	.header.links li .customer-name:active,
	.header.links li .customer-name.active {
		color: '.$header_account_link_color_a.';
		background-color: '.$header_account_link_bg_a.';
	}
	.header-wrapper .page-header .header.links li a:focus,
	.header.links li .customer-name:focus {
		color: '.$header_account_link_color_f.';
		background-color: '.$header_account_link_bg_f.';
	}
	.header-wrapper .page-header .customer-welcome ul {background-color: '.$header_account_sub_bg.';}
	.header-wrapper .page-header .customer-menu .header.links > li > a,
	.header-wrapper .page-header .customer-menu .header.links > li > a:hover {color: inherit; background-color: transparent;}
	.header-wrapper .page-header .customer-menu .header.links > li {
		color: '.$header_account_sub_link_color.';
		background-color: '.$header_account_sub_link_bg.';
	}
	.header-wrapper .page-header .customer-menu .header.links > li:hover {
		color: '.$header_account_sub_link_color_h.';
		background-color: '.$header_account_sub_link_bg_h.';
	}
	.header-wrapper .page-header .customer-menu .header.links > li:active {
		color: '.$header_account_sub_link_color_a.';
		background-color: '.$header_account_sub_link_bg_a.';
	}
	.header-wrapper .page-header .customer-menu .header.links > li:focus {
		color: '.$header_account_sub_link_color_f.';
		background-color: '.$header_account_sub_link_bg_f.';
	}

	#home-slider .item h2,
    #home-slider .item .slide-container .title {color: '.$header_slider_title_1.';}
    #home-slider .item h2 span,
    #home-slider .item h4,
    #home-slider .item h5,
    #home-slider .item p,
    #home-slider .item .slide-container.slide-skin-2 h2,
    #home-slider .item .slide-container.slide-skin-2 p,
    #home-slider .item .slide-container.slide-skin-2 h3,
    #home-slider .item .slide-container.slide-skin-3 h2,
    #home-slider .item .slide-container.slide-skin-3 h3,
    #home-slider .item .slide-container .subtitle {color: '.$header_slider_title_2.';}
    #home-slider .item h3,
    #home-slider .item .slide-container.slide-skin-2 h4,
    #home-slider .item .slide-container.slide-skin-3 h4 {color: '.$header_slider_title_3.';}
    #home-slider .item .slide-container.slide-skin-2 h3 span,
    #home-slider .item .slide-container.slide-skin-3 h3 span {color: '.$header_slider_title_4.';}
	#home-slider .owl-nav div {
		background-color: '.$header_slider_arrows_bg.';
		color: '.$header_slider_arrows_color.';
	}
	#home-slider .owl-nav div:hover {
		background-color: '.$header_slider_arrows_bg_h.';
		color: '.$header_slider_arrows_color_h.';
	}
	#home-slider .owl-nav div:active {
		background-color: '.$header_slider_arrows_bg_a.';
		color: '.$header_slider_arrows_color_a.';
	}
	#home-slider .owl-nav div:focus {
		background-color: '.$header_slider_arrows_bg_f.';
		color: '.$header_slider_arrows_color_f.';
	}
	#home-slider .item .slide-container .slider-link {
		background-color: '.$header_slider_btn_bg.';
		color: '.$header_slider_btn_color.';
	}
	#home-slider .item .slide-container .slider-link:hover {
		background-color: '.$header_slider_btn_bg_h.';
		color: '.$header_slider_btn_color_h.';
	}
	#home-slider .item .slide-container .slider-link:active {
		background-color: '.$header_slider_btn_bg_a.';
		color: '.$header_slider_btn_color_a.';
	}
	#home-slider .item .slide-container .slider-link:focus {
		background-color: '.$header_slider_btn_bg_f.';
		color: '.$header_slider_btn_color_f.';
	}
	.page-title,
	.product-info-main .page-title,
	.catalogsearch-advanced-result .page-title,
	.catalogsearch-result-index .page-title,
	.catalog-category-view .page-title {color: '.$content_title_color.';}
	.page-title-wrapper:not(.product) {border-color: '.$content_title_border.';}

	.toolbar label,
	.toolbar .label,
	.sorter .sorter-label {color: '.$content_toolar_label.';}
	.toolbar .toolbar-sorter select {
		background-color: '.$content_toolar_select_bg.';
		border-color: '.$content_toolar_select_border.';
		color: '.$content_toolar_select_color.';
	}
	.toolbar .toolbar-sorter select:hover {
		background-color: '.$content_toolar_select_bg_h.';
		border-color: '.$content_toolar_select_border_h.';
		color: '.$content_toolar_select_color_h.';
	}
	.toolbar .toolbar-sorter select:active {
		background-color: '.$content_toolar_select_bg_a.';
		border-color: '.$content_toolar_select_border_a.';
		color: '.$content_toolar_select_color_a.';
	}
	.toolbar .toolbar-sorter select:focus {
		background-color: '.$content_toolar_select_bg_f.';
		border-color: '.$content_toolar_select_border_f.';
		color: '.$content_toolar_select_color_f.';
	}
	.toolbar .toolbar-sorter select option {
		background-color: '.$content_toolar_select_dropdown_link_bg.';
		color: '.$content_toolar_select_dropdown_link_color.';
	}
	.toolbar .toolbar-sorter select option:hover {
		background-color: '.$content_toolar_select_dropdown_link_bg_h.';
		color: '.$content_toolar_select_dropdown_link_color_h.';
	}
	.toolbar .toolbar-sorter select option:active {
		background-color: '.$content_toolar_select_dropdown_link_bg_a.';
		color: '.$content_toolar_select_dropdown_link_color_a.';
	}
	.toolbar .toolbar-sorter select option:focus {
		background-color: '.$content_toolar_select_dropdown_link_bg_f.';
		color: '.$content_toolar_select_dropdown_link_color_f.';
	}
	.toolbar .modes a span {
		background-color: '.$content_toolar_grid_switcher_bg.';
		color: '.$content_toolar_grid_switcher_color.';
		border-color: '.$content_toolar_grid_switcher_border.';
	}
	.toolbar .modes a:hover span {
		background-color: '.$content_toolar_grid_switcher_bg_h.';
		color: '.$content_toolar_grid_switcher_color_h.';
		border-color: '.$content_toolar_grid_switcher_border_h.';
	}
	.toolbar .modes strong span {
		background-color: '.$content_toolar_grid_switcher_bg_a.';
		color: '.$content_toolar_grid_switcher_color_a.';
		border-color: '.$content_toolar_grid_switcher_border_a.';
	}
	.toolbar .modes a:focus span {
		background-color: '.$content_toolar_grid_switcher_bg_f.';
		color: '.$content_toolar_grid_switcher_color_f.';
		border-color: '.$content_toolar_grid_switcher_border_f.';
	}
	.toolbar .toolbar-sorter .sorter-action span {
		background-color: '.$content_toolar_desc_switcher_bg.';
		color: '.$content_toolar_desc_switcher_color.';
	}
	.toolbar .toolbar-sorter .sorter-action span:hover {
		background-color: '.$content_toolar_desc_switcher_bg_h.';
		color: '.$content_toolar_desc_switcher_color_h.';
	}
	.toolbar .toolbar-sorter .sorter-action span:active {
		background-color: '.$content_toolar_desc_switcher_bg_a.';
		color: '.$content_toolar_desc_switcher_color_a.';
	}
	.toolbar .toolbar-sorter .sorter-action span:focus {
		background-color: '.$content_toolar_desc_switcher_bg_f.';
		color: '.$content_toolar_desc_switcher_color_f.';
	}
	.toolbar .sidebar-button span {color: inherit;}
	.toolbar .sidebar-button {
		background-color: '.$content_toolar_sidebar_btn_bg.';
		color: '.$content_toolar_sidebar_btn_color.';
		border-color: '.$content_toolar_sidebar_btn_border.';
	}
	.toolbar .sidebar-button:hover {
		background-color: '.$content_toolar_sidebar_btn_bg_h.';
		color: '.$content_toolar_sidebar_btn_color_h.';
		border-color: '.$content_toolar_sidebar_btn_border_h.';
	}
	.toolbar .sidebar-button:active {
		background-color: '.$content_toolar_sidebar_btn_bg_a.';
		color: '.$content_toolar_sidebar_btn_color_a.';
		border-color: '.$content_toolar_sidebar_btn_border_a.';
	}
	.toolbar .sidebar-button:focus {
		background-color: '.$content_toolar_sidebar_btn_bg_f.';
		color: '.$content_toolar_sidebar_btn_color_f.';
		border-color: '.$content_toolar_sidebar_btn_border_f.';
	}

	.toolbar .pagination > li {border-color: '.$content_pager_btns_divider.';}
	.toolbar .pagination > li > a,
	.toolbar .pagination > li > span {
		color: '.$content_pager_btns_color.';
		background-color: '.$content_pager_btns_bg.';
		border-color: '.$content_pager_btns_border.';
	}
	.toolbar .pagination > li > a:hover,
	.toolbar .pagination > li > span:hover {
		color: '.$content_pager_btns_color_h.';
		background-color: '.$content_pager_btns_bg_h.';
		border-color: '.$content_pager_btns_border_h.';
	}
	.toolbar .pagination > .active > a,
	.toolbar .pagination > .active > span,
	.toolbar .pagination > .active > a:hover,
	.toolbar .pagination > .active > span:hover,
	.toolbar .pagination > .active > a:focus,
	.toolbar .pagination > .active > span:focus {
		color: '.$content_pager_btns_color_a.';
		background-color: '.$content_pager_btns_bg_a.';
		border-color: '.$content_pager_btns_border_a.';
	}
	.toolbar .pagination > li > a:focus,
	.toolbar .pagination > li > span:focus {
		color: '.$content_pager_btns_color_f.';
		background-color: '.$content_pager_btns_bg_f.';
		border-color: '.$content_pager_btns_border_f.';
	}
	.text-banner .text-banner-content .title,
	.text-banner .text-banner-content,
	.sidebar-banner .sidebar-banner-content .title,
	.sidebar-banner .sidebar-banner-content {color: '.$content_home_banners_title_color.';}
	.text-banner.four .text-banner-content .title,
	.sidebar-banner.two .sidebar-banner-content .title {color: '.$content_home_banners_title2_color.';}
	.text-banner.five .text-banner-content .subtitle,
	.text-banner.five .text-banner-content .text,
	.sidebar-banner:not(.two) .sidebar-banner-content .subtitle, 
	.sidebar-banner:not(.two) .sidebar-banner-content .text {color: '.$content_home_banners_subtitle_color.';}
	.text-banner .text-banner-content .banner-link,
	.category-banner .category-banner-content .category-banner-link {
		background-color: '.$content_home_banners_btn_bg.';
		color: '.$content_home_banners_btn_color.';
	}
	.text-banner .text-banner-content .banner-link:hover,
	.category-banner .category-banner-content .category-banner-link:hover {
		background-color: '.$content_home_banners_btn_bg_h.';
		color: '.$content_home_banners_btn_color_h.';
	}
	.text-banner .text-banner-content .banner-link:active,
	.category-banner .category-banner-content .category-banner-link:active {
		background-color: '.$content_home_banners_btn_bg_a.';
		color: '.$content_home_banners_btn_color_a.';
	}
	.text-banner .text-banner-content .banner-link:focus,
	.category-banner .category-banner-content .category-banner-link:focus {
		background-color: '.$content_home_banners_btn_bg_f.';
		color: '.$content_home_banners_btn_color_f.';
	}
	.sidebar .widget .features i {
		background-color: '.$content_sidebar_features_icon_bg.';
		border-color: '.$content_sidebar_features_icon_bg.';
		color: '.$content_sidebar_features_icon_color.';
	}
	
	.cms-no-route .page-header .form.minisearch .input-group {background-color: '.$not_found_search_bg.';}
	.cms-no-route .header-wrapper .block-search .input-group .btn span i {color: '.$not_found_search_icon.';}
	.cms-no-route .page-header .form.minisearch input {border-color: '.$not_found_search_border.'; color: '.$not_found_search_color.';}
	.cms-no-route .page-not-found h2 {color: '.$not_found_title_color.';}
	.cms-no-route .page-not-found h3 {color: '.$not_found_subtitle_color.';}
	.cms-no-route .page-not-found p {color: '.$not_found_text_color.';}
	.cms-no-route .page-not-found .btn {
		background-color: '.$not_found_btn_bg.';
		border-color: '.$not_found_btn_border.';
		color: '.$not_found_btn_color.';
	}
	.cms-no-route .page-not-found .btn:hover {
		background-color: '.$not_found_btn_bg_h.';
		border-color: '.$not_found_btn_border_h.';
		color: '.$not_found_btn_color_h.';
	}
	.cms-no-route .page-not-found .btn:active {
		background-color: '.$not_found_btn_bg_a.';
		border-color: '.$not_found_btn_border_a.';
		color: '.$not_found_btn_color_a.';
	}
	.cms-no-route .page-not-found .btn:focus {
		background-color: '.$not_found_btn_bg_f.';
		border-color: '.$not_found_btn_border_f.';
		color: '.$not_found_btn_color_f.';
	}
	.cms-no-route .footer .footer-links li a {
		color: '.$not_found_footer_links_color.';
		background-color: transparent;
	}
	.cms-no-route .footer .footer-links li:before {color: '.$not_found_footer_links_color.';}
	.cms-no-route .footer .footer-links li a:hover {color: '.$not_found_footer_links_color_h.';}
	.cms-no-route .footer .footer-links li a:active {color: '.$not_found_footer_links_color_a.';}
	.cms-no-route .footer .footer-links li a:focus {color: '.$not_found_footer_links_color_f.';}
	.cms-no-route .footer address {color: '.$not_found_copyright_color.';}
	.cms-no-route .footer address a {color: '.$not_found_copyright_link_color.';}
	.cms-no-route .footer address a:hover {color: '.$not_found_copyright_link_color_h.';}
	.cms-no-route .footer address a:active {color: '.$not_found_copyright_link_color_a.';}
	.cms-no-route .footer address a:focus {color: '.$not_found_copyright_link_color_f.';}

	body .btn,
	.actions-toolbar .action,
	body button,
	body button.primary,
	.actions-toolbar .primary .action,
	.actions-toolbar .secondary .action,
	body .btn-default.disabled:hover,
	body .btn-default[disabled]:hover,
	body fieldset[disabled] .btn-default:hover,
	body .btn-default.disabled:focus,
	body .btn-default[disabled]:focus,
	body fieldset[disabled] .btn-default:focus,
	body .btn-default.disabled.focus,
	body .btn-default[disabled].focus,
	body fieldset[disabled] .btn-default.focus,
	body .btn-primary.disabled:focus,
	body .btn-primary[disabled]:focus,
	body fieldset[disabled] .btn-primary:focus,
	body .btn-primary.disabled.focus,
	body .btn-primary[disabled].focus,
	body fieldset[disabled] .btn-primary.focus,
	body button.primary.checkout:hover,
	.products-grid a.btn.btn-default,
	.products-list a.btn.btn-default,
	.sidebar .block.filter .filter-actions .filter-clear.type-2,
	#contact-form-mini button {
		background-color: '.$buttons_type1_bg.';
		border-color: '.$buttons_type1_border.';
		color: '.$buttons_type1_color.';
	}
	.actions-toolbar .action:hover,
	body button.primary:hover,
	body .btn.btn-primary:hover,
	body .open > .dropdown-toggle.btn-primary:hover,
	body .btn-primary.disabled:hover,
	body .btn-primary[disabled]:hover,
	body fieldset[disabled] .btn-primary:hover,
	.actions-toolbar .primary .action:hover,
	.actions-toolbar .secondary .action:hover,
	body .btn:hover,
	body button:hover,
	body .btn.btn-primary.type-2:hover,
	#popup-block .block.newsletter .content button.primary:hover,
	body button.primary.checkout,
	body .btn.btn-primary,
	.products-grid a.btn.btn-default:hover,
	.products-list a.btn.btn-default:hover,
	.sidebar .block.filter .filter-actions .filter-clear.type-2:hover,
	#contact-form-mini button:hover {
		background-color: '.$buttons_type1_bg_h.';
		border-color: '.$buttons_type1_border_h.';
		color: '.$buttons_type1_color_h.';
	}
	.actions-toolbar .action:active,
	body button.primary:active,
	body .btn-primary.active:hover,
	body .btn-primary.active:focus,
	body .btn-primary:active.focus,
	body .btn-primary.active.focus,
	body .btn-primary.disabled:active,
	body .btn-primary[disabled]:active,
	body fieldset[disabled] .btn-primary:active,
	.actions-toolbar .primary .action:active,
	.actions-toolbar .secondary .action:active,
	body .btn:active,
	body button:active,
	body .btn.btn-primary.type-2:active,
	#popup-block .block.newsletter .content button.primary:active,
	.products-grid a.btn.btn-default:active,
	.products-list a.btn.btn-default:active,
	.sidebar .block.filter .filter-actions .filter-clear.type-2:active,
	#contact-form-mini button:active {
		background-color: '.$buttons_type1_bg_a.';
		border-color: '.$buttons_type1_border_a.';
		color: '.$buttons_type1_color_a.';
	}
	.actions-toolbar .action:focus,
	body button.primary:focus,
	body .open > .dropdown-toggle.btn-primary.focus,
	body .btn-primary.disabled:focus,
	body .btn-primary[disabled]:focus,
	body fieldset[disabled] .btn-primary:focus,
	.actions-toolbar .primary .action:focus,
	.actions-toolbar .secondary .action:focus,
	body .btn:focus,
	body button:focus,
	body .btn.btn-primary.type-2:focus,
	#popup-block .block.newsletter .content button.primary:focus,
	.products-grid a.btn.btn-default:focus,
	.products-list a.btn.btn-default:focus,
	.sidebar .block.filter .filter-actions .filter-clear.type-2:focus,
	#contact-form-mini button:focus {
		background-color: '.$buttons_type1_bg_f.';
		border-color: '.$buttons_type1_border_f.';
		color: '.$buttons_type1_color_f.';
	}

	.cart-container .cart.actions a.continue,
	body .btn.btn-primary.type-2,
	#popup-block .block.newsletter .content button.primary,
	.block-reorder .actions-toolbar .secondary .action,
	.block-compare .actions-toolbar .secondary .action {
		background-color: '.$buttons_type2_bg.';
		border-color: '.$buttons_type2_border.';
		color: '.$buttons_type2_color.';
	}
	.cart-container .cart.actions a.continue:hover,
	body .btn.btn-primary.type-2:hover,
	#popup-block .block.newsletter .content button.primary:hover,
	.block-reorder .actions-toolbar .secondary .action:hover,
	.block-compare .actions-toolbar .secondary .action:hover {
		background-color: '.$buttons_type2_bg_h.';
		border-color: '.$buttons_type2_border_h.';
		color: '.$buttons_type2_color_h.';
	}
	.cart-container .cart.actions a.continue:active,
	body .btn.btn-primary.type-2:active,
	#popup-block .block.newsletter .content button.primary:active,
	.block-reorder .actions-toolbar .secondary .action:active,
	.block-compare .actions-toolbar .secondary .action:active {
		background-color: '.$buttons_type2_bg_a.';
		border-color: '.$buttons_type2_border_a.';
		color: '.$buttons_type2_color_a.';
	}
	.cart-container .cart.actions a.continue:focus,
	body .btn.btn-primary.type-2:focus,
	#popup-block .block.newsletter .content button.primary:focus,
	.block-reorder .actions-toolbar .secondary .action:focus,
	.block-compare .actions-toolbar .secondary .action:focus {
		background-color: '.$buttons_type2_bg_f.';
		border-color: '.$buttons_type2_border_f.';
		color: '.$buttons_type2_color_f.';
	}
	
	body .product-info-main .btn.btn-primary {
		background-color: '.$buttons_productpage_cart_btn_bg.';
		border-color: '.$buttons_productpage_cart_btn_border.';
		color: '.$buttons_productpage_cart_btn_color.';
	}
	body .product-info-main .btn.btn-primary:hover {
		background-color: '.$buttons_productpage_cart_btn_bg_h.';
		border-color: '.$buttons_productpage_cart_btn_border_h.';
		color: '.$buttons_productpage_cart_btn_color_h.';
	}
	body .product-info-main .btn.btn-primary:active {
		background-color: '.$buttons_productpage_cart_btn_bg_a.';
		border-color: '.$buttons_productpage_cart_btn_border_a.';
		color: '.$buttons_productpage_cart_btn_color_a.';
	}
	body .product-info-main .btn.btn-primary:focus {
		background-color: '.$buttons_productpage_cart_btn_bg_f.';
		border-color: '.$buttons_productpage_cart_btn_border_f.';
		color: '.$buttons_productpage_cart_btn_color_f.';
	}
	.products-list li.item .weltpixel-quickview,
	.products-grid .item .weltpixel-quickview {
		background-color: '.$buttons_quickview_btn_bg.';
		color: '.$buttons_quickview_btn_color.';
	}
	.products-list li.item .weltpixel-quickview:hover,
	.products-grid .item .weltpixel-quickview:hover {
		background-color: '.$buttons_quickview_btn_bg_h.';
		color: '.$buttons_quickview_btn_color_h.';
	}
	.products-list li.item .weltpixel-quickview:active,
	.products-grid .item .weltpixel-quickview:active {
		background-color: '.$buttons_quickview_btn_bg_a.';
		color: '.$buttons_quickview_btn_color_a.';
	}
	.products-list li.item .weltpixel-quickview:focus,
	.products-grid .item .weltpixel-quickview:focus {
		background-color: '.$buttons_quickview_btn_bg_f.';
		color: '.$buttons_quickview_btn_color_f.';
	}
	.products-list li.item .lightbox-button,
	.products-grid .item .lightbox-button {
		background-color: '.$buttons_moreviews_btn_bg.';
		color: '.$buttons_moreviews_btn_color.';
	}
	.products-list li.item .lightbox-button:hover,
	.products-grid .item .lightbox-button:hover {
		background-color: '.$buttons_moreviews_btn_bg_h.';
		color: '.$buttons_moreviews_btn_color_h.';
	}
	.products-list li.item .lightbox-button:active,
	.products-grid .item .lightbox-button:active {
		background-color: '.$buttons_moreviews_btn_bg_a.';
		color: '.$buttons_moreviews_btn_color_a.';
	}
	.products-list li.item .lightbox-button:focus,
	.products-grid .item .lightbox-button:focus {
		background-color: '.$buttons_moreviews_btn_bg_f.';
		color: '.$buttons_moreviews_btn_color_f.';
	}
	.products-grid li.item:hover::before,
	.products-grid .owl-item:hover::before,
	.products-list li.item:hover::before {
		background-color: '.$products_bg.';
		border-color: '.$products_border.';
	}
	.products-grid .product-item-name a,
	.products-list .product-item-name a {color: '.$products_title_color.';}
	.products-grid .product-item-name a:hover,
	.products-list .product-item-name a:hover {color: '.$products_title_color_h.';}
	.products-grid .product-item-name a:active,
	.products-list .product-item-name a:active {color: '.$products_title_color_a.';}
	.products-grid .product-item-name a:focus,
	.products-list .product-item-name a:focus {color: '.$products_title_color_f.';}
	.products-list li.item,
	.products-grid .item {color: '.$products_text_color.';}
	.products-grid a,
	.products-list a {color: '.$products_link_color.';}
	.products-grid a:hover,
	.products-list a:hover {color: '.$products_link_color_h.';}
	.products-grid a:active,
	.products-list a:active {color: '.$products_link_color_a.';}
	.products-grid a:focus,
	.products-list a:focus {color: '.$products_link_color_f.';}
	.price-box .price,
	.price {color: '.$products_price_color.';}
	.old-price .price,
	.old-price .price {color: '.$products_old_price_color.';}
	.special-price .price,
	.special-price .price {color: '.$products_special_price_color.';}
	.products-grid .product-item-actions {border-color: '.$products_divider_color.';}

	.product-labels span.label-sale {background-color: '.$products_labels_sale_bg.'; color: '.$products_labels_sale_color.';}
	.product-labels span {background-color: '.$products_labels_new_bg.'; color: '.$products_labels_new_color.';}
	.label-type-4 .label-sale:before,
	.label-type-4.two-items .label-sale:before {border-top-color: '.$products_labels_sale_bg.';}
	.label-type-4.two-items .label-sale:before,
	.label-type-4.two-items .label-sale:after,
	.label-type-4 .label-sale:after {border-bottom-color: '.$products_labels_sale_bg.';}
	.label-type-4 .label-new::before {border-top-color: '.$products_labels_new_bg.';}
	.label-type-4 .label-new::after {border-bottom-color: '.$products_labels_new_bg.';}

	.timer-box div {background-color: '.$price_countdown_timer_bg.';}
	.timer-box div span {color: '.$price_countdown_timer_color.';}
	ul.social-links li a i {
		background-color: '.$social_links_bg.';
		border-color: '.$social_links_border.';
		color: '.$social_links_color.';
	}
	ul.social-links li a:hover i {
		background-color: '.$social_links_bg_h.';
		border-color: '.$social_links_border_h.';
		color: '.$social_links_color_h.';
	}
	ul.social-links li a:active i {
		background-color: '.$social_links_bg_a.';
		border-color: '.$social_links_border_a.';
		color: '.$social_links_color_a.';
	}
	ul.social-links li a:focus i {
		background-color: '.$social_links_bg_f.';
		border-color: '.$social_links_border_f.';
		color: '.$social_links_color_f.';
	}
	.footer .footer-topline .footer-block-title h5 {color: '.$footer_top_title_color.';}
	.footer .footer-topline .footer-block-title .right-divider {border-color: '.$footer_top_title_border.';}
	.footer .custom-footer-content.features li > div h3 {color: '.$footer_top_subtitle_color.';}
	body.wide-layout .footer .footer-topline,
	body.boxed-layout .footer .footer-topline .container {background-color: '.$footer_top_bg.';}
	.footer .custom-footer-content.features li > div p,
	.footer .footer-topline p,
	.footer .footer-topline #contact-form-mini label {color: '.$footer_top_text_color.';}
	.footer .footer-topline ul.links li a {
		color: '.$footer_top_link_color.';
		background-color: '.$footer_top_link_bg.';
	}
	.footer .footer-topline ul.links li:hover:after,
	.footer .footer-topline ul.links li:after {
		color: '.$footer_top_link_bg_h.';
	}
	.footer .footer-topline ul.links li a:hover {
		color: '.$footer_top_link_color_h.';
		background-color: transparent;
	}
	.footer .footer-topline ul.links li a:active {
		color: '.$footer_top_link_color_a.';
		background-color: transparent;
	}
	.footer .footer-topline ul.links li:active:after {
		background-color: '.$footer_top_link_bg_a.';
	}
	.footer .footer-topline ul.links li a:focus {
		color: '.$footer_top_link_color_f.';
		background-color: transparent;
	}
	.footer .footer-topline ul.links li:focus:after {
		background-color: '.$footer_top_link_bg_f.';
	}
	.footer .custom-footer-content.features i {
		background-color: '.$footer_top_icon_block_bg.';
		border-color: '.$footer_top_icon_block_border.';
		color: '.$footer_top_icon_block_color.';
	}
	.footer .custom-footer-content.features i:hover {
		background-color: '.$footer_top_icon_block_bg_h.';
		color: '.$footer_top_icon_block_color_h.';
	}
	.footer .custom-footer-content.features i:active {
		background-color: '.$footer_top_icon_block_bg_a.';
		color: '.$footer_top_icon_block_color_a.';
	}
	.footer .custom-footer-content.features i:focus {
		background-color: '.$footer_top_icon_block_bg_f.';
		color: '.$footer_top_icon_block_color_f.';
	}

	.footer .footer-second-line .footer-block-title h5 {
		color: '.$footer_middle_title_color.';
	}
	.footer .footer-second-line .footer-block-title .right-divider {
		border-color: '.$footer_middle_title_border_color.';
	}
	body.wide-layout .footer .footer-second-line,
	body.boxed-layout .footer .footer-second-line .container {
		background-color: '.$footer_middle_bg.';
		color: '.$footer_middle_text_color.';
	}
	.footer .footer-second-line .footer-address-block p,
	.footer .footer-address-block p a {color: '.$footer_middle_text_color.';}
	.footer .footer-second-line ul.links li a,
	.footer .footer-second-line .footer-links li a {
		color: '.$footer_middle_link_color.';
		background-color: '.$footer_middle_link_bg.';
	}
	.footer .footer-second-line ul.links li:hover:after,
	.footer .footer-second-line ul.links li:after {
		background-color: '.$footer_middle_link_bg_h.';
	}
	.footer .footer-second-line ul.links li a:hover {
		color: '.$footer_middle_link_color_h.';
		background-color: transparent;
	}
	.footer .footer-second-line .footer-links li a:hover {
		background-color: '.$footer_middle_link_bg_h.';
		color: '.$footer_middle_link_color_h.';
	}
	.footer .footer-second-line ul.links li a:active {
		color: '.$footer_middle_link_color_a.';
		background-color: transparent;
	}
	.footer .footer-second-line .footer-links li a:active {
		background-color: '.$footer_middle_link_bg_a.';
		color: '.$footer_middle_link_color_a.';
	}
	.footer .footer-second-line ul.links li:active:after {
		background-color: '.$footer_middle_link_bg_a.';
	}
	.footer .footer-second-line ul.links li a:focus {
		color: '.$footer_middle_link_color_f.';
		background-color: transparent;
	}
	.footer .footer-second-line .footer-links li a:focus {
		background-color: '.$footer_middle_link_bg_f.';
		color: '.$footer_middle_link_color_f.';
	}
	.footer .footer-second-line ul.links li:focus:after {
		background-color: '.$footer_middle_link_bg_f.';
	}
	body.wide-layout .footer .footer-bottom-wrapper,
	body.boxed-layout .footer .footer-bottom-wrapper .container {
		background-color: '.$footer_bottom_bg.';
		color: '.$footer_bottom_text_color.';
	}
	.page-footer .footer .footer-bottom-wrapper .switcher strong,
	.footer .footer-bottom-wrapper address {
		color: '.$footer_bottom_text_color.';
	}
	.footer address a {color: '.$footer_bottom_link_color.';}
	.footer address a:hover {color: '.$footer_bottom_link_color_h.';}
	.footer address a:active {color: '.$footer_bottom_link_color_a.';}
	.footer address a:focus {color: '.$footer_bottom_link_color_f.';}

	';
		return $cssData;
	}
}