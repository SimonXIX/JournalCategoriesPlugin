{**
 * templates/settingsForm.tpl
 *
 * Journal Categories plugin settings form.
 * Each line: Category Name | id1, id2, id3 | Optional description
 *}

<script>
	$(function() {ldelim}
		$('#journalCategoriesSettingsForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>

<form
	class="pkp_form"
	id="journalCategoriesSettingsForm"
	method="POST"
	action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="settings" save=true}"
>
	{csrf}

	<div id="journalCategoriesSettingsFormFields">

		<div class="section">
			<label for="categoriesText"><strong>Journal Categories</strong></label>
			<p class="instruct">
				One category per line in the format:<br>
				<code>Category Name | id1, id2, id3 | Optional description</code><br>
				Lines starting with # are ignored. Journals not listed will appear under "Other Journals".
			</p>
			<textarea
				name="categoriesText"
				id="categoriesText"
				rows="20"
				style="width:100%; font-family: monospace; font-size: 0.9em;"
			>{$categoriesText|escape}</textarea>
		</div>

	</div>

	<p>
		<button type="submit" class="pkp_button">{translate key="common.save"}</button>
	</p>

</form>
