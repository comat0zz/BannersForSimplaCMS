<?php

	require_once('Simpla.php');

	class Banner extends Simpla
	{

		/**
		 * Получаем список всех баннерных категорий.
		 * @return array
		 */
		public function getCategories()
		{
			$query = $this->db->placehold('SELECT * FROM __banners_categories');
			$this->db->query($query);
			return $this->db->results();
		}


		/**
		 * Получить категорию по ID
		 * @param $category_id
		 * @return bool|object
		 */
		public function getCategory($category_id)
		{
			$query = $this->db->placehold('SELECT * FROM __banners_categories WHERE id=? LIMIT 1', (int)$category_id);
			$this->db->query($query);
			return $this->db->result();
		}

		/**
		 * Получить категорию по псевдо имени.
		 * @param $category_id
		 * @return bool|object
		 */
		public function getCategoryByMnem($mnemonic)
		{
			$query = $this->db->placehold('SELECT * FROM __banners_categories WHERE mnemonic=? AND enabled=1 LIMIT 1', $mnemonic);
			$this->db->query($query);
			return $this->db->result();
		}

		/**
		 * Добавление новой категории.
		 * @param $category
		 * @return int
		 */
		public function addCategory($category)
		{
			$query = $this->db->placehold('INSERT INTO __banners_categories SET ?%', $category);
			$this->db->query($query);
			return $this->db->insert_id();
		}


		/**
		 * Обновление списка категорий.
		 * @param $id
		 * @param $category
		 * @return mixed
		 */
		public function updateCategory($id, $category)
		{
			$query = $this->db->placehold('UPDATE __banners_categories SET ?% WHERE id in(?@) LIMIT ?',
				(array)$category, (array)$id, count((array)$id));
			$this->db->query($query);
			return $id;
		}


		/**
		 * Удаление категории.
		 * @param $id
		 */
		public function deleteCategory($id)
		{
			$query = $this->db->placehold('DELETE FROM __banners_categories WHERE id=? LIMIT 1', (int)$id);
			$this->db->query($query);
			$this->deleteElementsAll($id);
		}

		/**
		 * Удаление изображения у элемента.
		 * @param $element_id
		 */
		public function deleteElementImage($element_id)
		{
			$query = $this->db->placehold('SELECT image FROM __banners WHERE id=?', $element_id);
			$this->db->query($query);
			$filename = $this->db->result('image');
			$query = $this->db->placehold('SELECT 1 FROM __banners WHERE image=? AND id!=?', $filename, $element_id);
			$this->db->query($query);
			$exists = $this->db->num_rows();
			if(!empty($filename) && $exists == 0)
			{
				@unlink($this->config->root_dir.'/'.$this->config->original_images_dir.$filename);
			}

			$this->updateElement($element_id, array('image' => null, 'enabled' => 0));
		}


		/**
		 * Обновление существующего варианта.
		 * @param $element_id
		 * @param $element
		 * @return mixed
		 */
		public function updateElement($element_id, $element)
		{
			$query = $this->db->placehold('UPDATE __banners SET ?% WHERE id=? LIMIT 1', $element, (int)$element_id);
			$this->db->query($query);
			return $element_id;
		}

		/**
		 * Добавление нового элемента.
		 * @param $element
		 * @return int
		 */
		public function addElement($element)
		{
			$query = $this->db->placehold('INSERT INTO __banners SET ?%', $element);
			$this->db->query($query);
			return $this->db->insert_id();
		}

		/**
		 * Получить список элементов по фильтру.
		 * @param array $filter
		 * @return array|bool
		 */
		public function getElements($filter = array())
		{
			$category_id = '';
			$sorted = ' b.sort ASC ';
			$enabled = '';
			$limit = '';

			if(!empty($filter) && isset($filter['category_id']))
			{
				$category_id = $this->db->placehold(' AND b.category_id=? ', (int)$filter['category_id'] );
			}

			if(!empty($filter) && isset($filter['limit']))
			{
				$limit = $this->db->placehold(' LIMIT ? ', (int)$filter['limit']);
			}

			if(!empty($filter) && isset($filter['sort']))
			{

				switch($filter['sort'])
				{
					case 'ASC':
						$sorted = $this->db->placehold(' b.sort ASC ');
						break;
					case 'DESC':
						$sorted = $this->db->placehold(' b.sort DESC ');
						break;
					default:
						$sorted = $this->db->placehold(' RAND() ');
						break;
				}
			}

			if(!empty($filter) && isset($filter['enabled']))
			{
				$enabled = $this->db->placehold(' AND b.enabled=? ', (int)$filter['enabled']);
			}

			$query = $this->db->placehold("SELECT b.* FROM __banners b WHERE 1
				$enabled
				$category_id
				ORDER BY $sorted $limit
			");

			$this->db->query($query);
			return $this->db->results();
		}

		/**
		 * Удаление элемента.
		 * @param $id
		 */
		public function deleteElement($id)
		{
			if(!empty($id))
			{
				$this->deleteElementImage($id);
				$query = $this->db->placehold('DELETE FROM __banners WHERE id = ? LIMIT 1', (int)$id);
				$this->db->query($query);
			}
		}


		/**
		 * Удалить все элементы категории.
		 * @param $category_id
		 */
		public function deleteElementsAll($category_id)
		{
			$elements = $this->getElements(array('category_id' => $category_id));
			foreach($elements as $el)
			{
				$this->deleteElement($el->id);
			}
		}
	}
