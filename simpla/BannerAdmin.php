<?php

	require_once('api/Simpla.php');

	class BannerAdmin extends Simpla
	{
		public function fetch()
		{

			if($this->request->method('post'))
			{
				$category			= new stdClass();
				$category->id		= $this->request->post('category_id');
				$category->name		= $this->request->post('name', 'string');
				$category->limited	= $this->request->post('limited', 'integer');
				$category->mnemonic	= $this->request->post('mnemonic', 'string');
				$category->sorted	= $this->request->post('sorted', 'string');
				$category->enabled	= $this->request->post('enabled', 'boolean');

				if(empty($category->id))
				{
					$category->id = null;
					$category->id = $this->banner->addCategory($category);
				}
				else
				{
					$this->banner->updateCategory($category->id, $category);
				}


				if($this->request->post('elements'))
				{
					$elements = array();
					foreach($this->request->post('elements') as $n => $ea)
					{
						foreach($ea as $i => $v)
						{
							$elements[$i]->$n = $v;
						}

					}

					if(!empty($elements))
					{
						$elements_ids = array();
						foreach($elements as $index => &$element)
						{

							if(!empty($_POST['delete_image'][$index]))
							{
								$this->banner->deleteElementImage($element->id);
							}

							if(!empty($_FILES['image']['tmp_name'][$index]) && !empty($_FILES['image']['name'][$index]))
							{
								$image_tmp_name = $_FILES['image']['tmp_name'][$index];
								$ext = pathinfo($_FILES['image']['name'][$index], PATHINFO_EXTENSION);

								$image_name = md5($_FILES['image']['name'][$index].time()).'.'.$ext;

								move_uploaded_file($image_tmp_name, $this->config->root_dir.'/'.$this->config->original_images_dir.$image_name);
								$element->image = $image_name;
							}

							$element->category_id = $category->id;

							if($element->id)
							{
								$this->banner->updateElement($element->id, $element);
							}
							else
							{
								$element->id = $this->banner->addElement($element);
							}

							$elements_ids[] = $element->id;
						}

						$currentElements = $this->banner->getElements(array('category_id'=>$category->id));
						foreach($currentElements as $curr)
						{
							if(!in_array($curr->id, $elements_ids))
							{
								$this->banner->deleteElement($curr->id);
							}
						}


						asort($elements_ids);
						$i = 0;
						foreach($elements_ids as $element_id)
						{
							$this->banner->updateElement($elements_ids[$i], array('sort'=>$element_id));
							$i++;
						}


					}
				}
				else
				{
					$this->banner->deleteElementsAll($category->id);
				}

				$id = $category->id;
			}
			else
			{
				$id = $this->request->get('id', 'integer');
			}

			$category = $this->banner->getCategory($id);
			$this->design->assign('category', $category);

			$elements = $this->banner->getElements(array('category_id' => $id));

			$this->design->assign('elements', $elements);

			return $this->design->fetch('banner.tpl');
		}
	}
