<?php

	require_once('api/Simpla.php');

	class BannersAdmin extends Simpla
	{
		public function fetch()
		{

			if( ($mode = $this->request->get('mode', 'string')) && $mode === 'delete' )
			{
				$id = $this->request->get('id', 'integer');
				$this->banner->deleteCategory($id);
			}

			$categories = $this->banner->getCategories();
			$this->design->assign('categories', $categories);

			return $this->design->fetch('banners.tpl');
		}
	}
