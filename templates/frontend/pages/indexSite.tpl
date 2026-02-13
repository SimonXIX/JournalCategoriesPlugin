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
                        <ul>
                                <div class="journal-category">
                                        <h3 id="active_journals">
                                                Active Journals
                                        </h3>
                                        <div id="see_more"> See <a href="#archived_journals">Archived Journals</a></div>
                                </div>
                                {foreach from=$journals item=journal}
                                    {if $journal->getId() == 1 || $journal->getId() == 2 || $journal->getId() == 4 || $journal->getId() == 11 || $journal->getId() == 6 || $journal->getId() == 17 || $journal->getId() == 16 || $journal->getId() == 5 || $journal->getId() == 9 || $journal->getId() == 13 || $journal->getId() == 15}
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
                                    {/if}
                                {/foreach}
                                <div class="journal-category">
                                        <h3 id="archived_journals">
                                                Archived Journals
                                        </h3>
                                        <div id="see_more"> See <a href="#active_journals">Active Journals</a></div>
                                </div>
                                {foreach from=$journals item=journal}
                                    {if $journal->getId() == 12 || $journal->getId() == 14 || $journal->getId() == 7 || $journal->getId() == 8 || $journal->getId() == 18 || $journal->getId() == 10}
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
                                    {/if}
                                {/foreach}
                        </ul>
                {/if}
        </div>

</div><!-- .page -->

{include file="frontend/components/footer.tpl"}