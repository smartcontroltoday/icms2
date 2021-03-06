<?php

class actionGroupsItemChildsView extends cmsAction {

    public function run($ctype, $item, $childs, $content_controller){

        if(!empty($childs['tabs'][$this->name]['relation_id'])){
            $relation = $childs['relations'][$childs['tabs'][$this->name]['relation_id']];
        } else {
            cmsCore::error404();
        }

        if (!in_array($relation['layout'], array('tab', 'hidden'))) {
            cmsCore::error404();
        }

        if (!empty($relation['options']['dataset_id'])){

            $dataset = cmsCore::getModel('content')->getContentDataset($relation['options']['dataset_id']);

            if ($dataset){
                $this->model->applyDatasetFilters($dataset);
            }

        }

        $filter =   "r.parent_ctype_id = '{$ctype['id']}' AND ".
                    "r.parent_item_id = '{$item['id']}' AND ".
                    'r.child_ctype_id IS NULL AND '.
                    "r.child_item_id = i.id AND r.target_controller = '{$this->name}'";

        $this->model->joinInner('content_relations_bind', 'r', $filter);

        if (!empty($relation['options']['limit'])){
            $this->setOption('limit', $relation['options']['limit']);
        }

        if (!empty($relation['options']['is_hide_filter'])){
            $this->setOption('is_filter', false);
        }

        $html = $this->renderGroupsList(href_to($ctype['name'], $item['slug'].'/view-'.$this->name));

        $seo_title = empty($relation['seo_title']) ? LANG_GROUPS . ' - ' . $item['title'] : string_replace_keys_values($relation['seo_title'], $item);
        $seo_keys  = empty($relation['seo_keys']) ? '' : string_replace_keys_values($relation['seo_keys'], $item);
        $seo_desc  = empty($relation['seo_desc']) ? '' : string_get_meta_description(string_replace_keys_values($relation['seo_desc'], $item));

        $this->cms_template->setContext($content_controller);

        return $this->cms_template->render('item_childs_view', array(
            'ctype'       => $ctype,
            'child_ctype' => array('name' => $this->name, 'title' => LANG_GROUPS),
            'item'        => $item,
            'childs'      => $childs,
            'html'        => $html,
            'relation'    => $relation,
            'seo_title'   => $seo_title,
            'seo_keys'    => $seo_keys,
            'seo_desc'    => $seo_desc
        ));

	}

}
