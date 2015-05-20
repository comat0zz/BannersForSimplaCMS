{capture name=tabs}
{if in_array('settings', $manager->permissions)}<li><a href="index.php?module=SettingsAdmin">Настройки</a></li>{/if}
{if in_array('currency', $manager->permissions)}<li><a href="index.php?module=CurrencyAdmin">Валюты</a></li>{/if}
{if in_array('delivery', $manager->permissions)}<li><a href="index.php?module=DeliveriesAdmin">Доставка</a></li>{/if}
{if in_array('payment', $manager->permissions)}<li><a href="index.php?module=PaymentMethodsAdmin">Оплата</a></li>{/if}
{if in_array('managers', $manager->permissions)}<li><a href="index.php?module=ManagersAdmin">Менеджеры</a></li>{/if}
{if in_array('labels', $manager->permissions)}
<li><a href="{url module=OrdersLabelsAdmin keyword=null keywordstatus=null id=null page=null label=null}">Метки</a></li>
{/if}

<li class="active"><a href="index.php?module=BannersAdmin">Баннеры</a></li>
{/capture}

{$meta_title = "Баннеры" scope=parent}

{* Заголовок *}
<div id="header">
	<h1>{if !$category->id}Добавить{else}Редактировать{/if} категорию</h1>
</div>

<link href="design/css/banners.css" rel="stylesheet" type="text/css" />

<form method="post" id="product" enctype="multipart/form-data">
	<input type=hidden name="session_id" value="{$smarty.session.id}">
	<div id="name">
		<input class="name" name=name type="text" value="{$category->name|escape}" placeholder="Название категории" />
		<div class="checkbox">
			<label><input type="checkbox" name="enabled" value="1"{if $category->enabled} checked{/if} /> Активен</label>
		</div>
		<input name=category_id type="hidden" value="{$category->id|escape}"/>
	</div>

	<div class="block layer">
		<h2>Параметры</h2>
		<ul>
			<li><label class=property>Псевдо имя<br>для вызова в ф-ии</label>
				<input class="name" name=mnemonic type="text" value="{$category->mnemonic|escape}" />
			</li>
			<li><label class=property>Сортировка</label>
				<select name="sorted">
					<option value="RND"{if $category->sorted == 'RND'} selected{/if}>Рандом</option>
					<option value="ASC"{if $category->sorted == 'ASC'} selected{/if}>По возрастанию</option>
					<option value="DESC"{if $category->sorted == 'DESC'} selected{/if}>По убыванию</option>
				</select>
			</li>
			<li><label class=property>Ограничение<br><i>0 - выкл</i></label><input class="name" name="limited" type="text" value="{$category->limited|escape}" /></li>
		</ul>
	</div>



	<div id="elements_block">
		<span class="add" id="add_element"><i class="dash_link">Добавить элемент</i></span>
		<div id="elements">
			{foreach from=$elements item=el}
			<ul>
				<li class="element_move">
					<div class="move_zone"></div>
				</li>
				<li class="element_show_image">
					{if $el->image}
					<img src="{$el->image|resize:40:40}" />
					{/if}
				</li>
				<li class="element_enabled">
					<select name="elements[enabled][]">
						<option value="1"{if $el->enabled} selected{/if}>Вкл</option>
						<option value="0"{if !$el->enabled} selected{/if}>Выкл</option>
					</select>
				</li>
				<li class="element_name">
					<input name="elements[id][]" type="hidden" value="{$el->id}" />
					<input name="elements[name][]" type="text" value="{$el->name|escape}" placeholder="Название, title" />
				</li>
				<li class="element_url">
					<input name="elements[url][]" type="text" value="{$el->url|escape}" placeholder="Ссылка" />
				</li>
				<li class="element_image">
					<a href="javascript:{}" class="add_image"><img src="design/images/cd_add.png" alt="" /></a>
					<div class="browse_image" style="display:none;">
						<input type=file name=image[]>
						<input type=hidden name=delete_image[]>
					</div>
				</li>
				<li>
					<a class="del_element" href="javascript:{}"><img src="design/images/cross-circle-frame.png" alt="" /></a>
				</li>
			</ul>

			{/foreach}
		</div>
	</div>

	<ul id="new_element" style='display:none;'>
		<li class="element_move">
			<div class="move_zone"></div>
		</li>
		<li class="element_enabled">
			<select name="elements[enabled][]">
				<option value="1">Вкл</option>
				<option value="0">Выкл</option>
			</select>
		</li>
		<li class="element_name">
			<input name="elements[id][]" type="hidden" value="" />
			<input name="elements[name][]" type="text" value="" placeholder="Название, title" />
		</li>
		<li class="element_url">
			<input name="elements[url][]" type="text" value="" placeholder="Ссылка" />
		</li>
		<li class="element_image">
			<a href="javascript:{}" class="add_image"><img src="design/images/cd_add.png" alt="" /></a>
			<div class="browse_image" style="display:none;">
				<input type=file name=image[]>
				<input type=hidden name=delete_image[]>
			</div>
		</li>
		<li>
			<a class="del_element" href="javascript:{}"><img src="design/images/cross-circle-frame.png" alt="" /></a>
		</li>
	</ul>

	<input class="button_green button_save" type="submit" name="" value="Сохранить" />
</form>

<script type="text/javascript">
	$(function() {

		var element = $('#new_element').clone(true);
		$('#new_element').remove().removeAttr('id');
		$('body').on('click', '#elements_block span.add', function() {
			$(element).remove()
				.clone(true)
				.appendTo('#elements')
				.fadeIn('slow')
				.find("input[name*=element][name*=name]")
				.focus()
		});

		$('body').on('click', '#elements_block a.del_element', function(){
			$(this).closest('ul')
				.fadeOut(200, function() { $(this).remove() })
		});

		$('body').on('click', '#elements_block a.add_image', function() {
			$(this).hide();
			$(this).closest('li')
				.find('div.browse_image')
				.show('fast');
			$(this).closest('li')
				.find('input[name*=image]')
				.attr('disabled', false);
		});

		$("#elements_block").sortable({ items: '#elements ul' , axis: 'y',  handle: '.move_zone' });

	});
</script>