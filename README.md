# BannersForSimplaCMS


Расширение для SimplaCMS для управления баннерными галлереями. 
Тестировалось на версии 2.3.6, но вероятно будет работать и на остальных.

Возможности:
* Создание несколько баннерных галерей.
* Настройка каждой галереи: порядок сортировки баннеров, вкл\выкл.
* Настройка каждого баннера включает в себя: изобрадение, ссылку, название(title), вкл\выкл.

Было добавлено мнемоническое имя категории, ибо забирать по ид не очень удобно, когда на странице множество вызовов. Так хоть видно где какая вызывается.  Плюс мнемоническое имя можно впоследствии налепить на "вторую партию" баннеров, без необхидимости смены ида в шаблоне. Вообщем, писалось под себя, кому не удобно - подправить там просто очень. 

Для включения расширения необходимо:

Выполнить SQL код в базе данных с магазином, который лежит в файле dump.sql
Скопировать все файлы по директориям, как есть. Таких файлов в SimplaCMS нет, потому ничего замениться не должно.

В ./api/Simpla.php в массив $Classes добавить элемент:
```php
'banner' 	=> 'Banners', 
```

В ./simpla/IndexAdmin.php в массив $modules_permissions добавить элемент:
```php
'BannersAdmin'		  => 'settings',
'BannerAdmin'		  => 'settings',
```

В ./simpla/design/html/settings.tpl (и в остальные табы, с которыми висит settings) в табы добавить:
```php
{if in_array('settings', $manager->permissions)}<li><a href="index.php?module=BannersAdmin">Баннеры</a></li>{/if}
```

В ./view/View.php подключаем плагин:
```php
$this->design->smarty->registerPlugin("function", "get_banners",               array($this, 'get_banners_plugin'));
```
И код самого плагина:
```php
	public function get_banners_plugin($params, &$smarty)
	{
		if($params['name'])
		{
			if( ($category = $this->banner->getCategoryByMnem($params['name']) ))
			{

				$elfilter = array();
				$elfilter['category_id'] = $category->id;
				$elfilter['sort'] = $category->sorted;
				$elfilter['enabled'] = 1;
				if($category->limited > 0)
				{
					$elfilter['limit'] = (int)$category->limited;
				}

				$elements = $this->banner->getElements($elfilter);
				$smarty->assign($params['var'], $elements);
			}
		}
	}
```


А теперь вызов в любом месте в шаблоне, вместо test псевдоимя указанное в настройках:

```php
	{get_banners var=banners name='test'}
	{if $banners}
    <div class="banners">
        <ul>
		{foreach from=$banners item=banner}
          <li><a href="{$banner->url|escape}" title="{$banner->name|escape}"><img src="{$banner->image|resize:280:230}" alt="{$banner->name|escape}"></a></li>
		{/foreach}
        </ul>
    </div>
	{/if}
```
