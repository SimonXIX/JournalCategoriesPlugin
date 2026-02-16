{**
 * templates/frontend/pages/indexSite.tpl
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Site index.
 *
 *}
{include file="frontend/components/header.tpl"}

<div class="page_index_site">

        {if $about}
                <div class="about_site">
                        {$about}
                </div>
        {/if}

        {include file="frontend/objects/announcements_list.tpl" numAnnouncements=$numAnnouncementsHomepage}

        <div class="journals">
                <h2>
                        {translate key="context.contexts"}
                </h2>
                {if !$journals|@count}
                        {translate key="site.noJournals"}
                {else}
                        {* Display categorized journals *}
                        {if $categorizedJournals}
                                {foreach from=$categorizedJournals key=categoryName item=categoryData}
                                        <ul>
                                                {if $categoryData.journals|@count > 0}
                                                        <div class="journal-category" id="{$categoryName|regex_replace:'/[^a-z0-9]+/':'_'|lower}">
                                                                <h3 class="category-title">{$categoryName|escape}</h3>
                                                                <!--{if $categoryData.description}
                                                                        <p class="category-description">{$categoryData.description|escape}</p>
                                                                {/if}-->
                                                                <div class="category-see-more">
                                                                        See also: 
                                                                        {assign var="currentCategory" value=$categoryName}
                                                                        {assign var="linkCount" value=0}
                                                                        {foreach from=$categorizedJournals key=otherCategoryName item=otherCategoryData}
                                                                                {if $otherCategoryName != $currentCategory && $otherCategoryData.journals|@count > 0}
                                                                                        {if $linkCount > 0}, {/if}
                                                                                        <a href="#{$otherCategoryName|regex_replace:'/[^a-z0-9]+/':'_'|lower}">{$otherCategoryName|escape}</a>
                                                                                        {assign var="linkCount" value=$linkCount+1}
                                                                                {/if}
                                                                        {/foreach}
                                                                </div>
                                                        </div>
                                                        {foreach from=$categoryData.journals item=journal}
                                                                {capture assign="url"}{url journal=$journal->getPath()}{/capture}
                                                                {assign var="thumb" value=$journal->getLocalizedData('journalThumbnail')}
                                                                {assign var="description" value=$journal->getLocalizedDescription()}
                                                                <li{if $thumb} class="has_thumb"{/if}>
                                                                        {if $thumb}
                                                                                <div class="thumb">
                                                                                        <a href="{$url}">
                                                                                                <img src="{$journalFilesPath}{$journal->getId()}/{$thumb.uploadName|escape:"url"}"{if $thumb.altText} alt="{$thumb.altText|escape|default:''}"{/if}>
                                                                                        </a>
                                                                                </div>
                                                                        {/if}

                                                                        <div class="body">
                                                                                <h3>
                                                                                        <a href="{$url}" rel="bookmark">
                                                                                                {$journal->getLocalizedName()|escape}
                                                                                        </a>
                                                                                </h3>
                                                                                {if $description}
                                                                                        <div class="description">
                                                                                                {$description}
                                                                                        </div>
                                                                                {/if}
                                                                                <ul class="links">
                                                                                        <li class="view">
                                                                                                <a href="{$url}">
                                                                                                        {translate key="site.journalView"}
                                                                                                </a>
                                                                                        </li>
                                                                                        <li class="current">
                                                                                                <a href="{url journal=$journal->getPath() page="issue" op="current"}">
                                                                                                        {translate key="site.journalCurrent"}
                                                                                                </a>
                                                                                        </li>
                                                                                </ul>
                                                                        </div>
                                                                </li>
                                                        {/foreach}
                                                {/if}
                                        </ul>
                                {/foreach} 
                        {/if} 
                {/if}
        </div>

</div><!-- .page -->

{include file="frontend/components/footer.tpl"}