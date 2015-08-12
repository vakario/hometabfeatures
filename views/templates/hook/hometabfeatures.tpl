{if $features}
<div id="back_hometabfeatures">
    <div id="hometabfeatures">
    	<div id="hometab_tabs">
            <div id="content_container" class="container">
			{assign var=valu value=0}
                {foreach from=$features item=feat key=k}
                {if $feat.active == 1}
                    <div class="content_bloc col-sm-4 col-md-4 activ" id="content_bloc{$valu}">
                        <!--<img src="modules/hometabfeatures/img/ESI-arrow_open.png" class="close_arrow">-->
						<span class="close_arrow">
							<span class="close_text">{l s='Fermer' mod='hometabfeatures'}</span>
							<span class="close_cross"></span>
							<span class="close_arrow_arrow"></span>
						</span>
                        <div class="content_img">
                            <img src="{$modules_dir}{$module_hometab}/img/{$feat.logo}">
                        </div>
                        <h3 class="content_title">
                            {$feat.title}
                        </h3>
                        <div class="content_text">
                            {$feat.content_text}
                        </div>
                        <div class="desc_plus">+ {l s='En savoir plus' mod='hometabfeatures'}</div>
                    </div>
				{assign var=valu value=$valu+1}
                {/if}
                {/foreach}
            </div>
        </div>
        <div id="first_tab_edit" class="container">
        <div class="row" id="hometab_edit">
		{assign var=val value=0}
             {foreach from=$features item=feat key=k}
             {if $feat.active == 1}
                <div class="description_bloc container col-md-12" id="description_bloc{$val}">
                    <div class="col-md-12 description_title">{$feat.description_title} </div>
                    <div class="col-md-4 description_image"><img src="{$modules_dir}{$module_hometab}/img/{$feat.image}"></div>
                    <div class="col-md-8 description_content1">
                    	<div class="col-md-12 description_content">
                        	{if $feat.description_content}<div class="col-xs-12 col-sm-4 col-md-4">{$feat.description_content}</div>{/if}
                           	<div class="col-xs-12 {if $feat.description_content}col-sm-8 col-md-8{else}col-sm-12 col-md-12{/if}">{$feat.description_content_right}</div>
                        </div>
                    </div>
                </div>
				{assign var=val value=$val+1}
             {/if}
             {/foreach}
         </div>
       </div>
 	</div>
</div>
{/if}