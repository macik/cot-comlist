<!-- BEGIN: MAIN -->

<h3 class="icon-comments cap" id="icomments">
	{PHP.L.comments_comments}
</h3>

<div class="unit lines">
<ul>
<!-- BEGIN: COM_ROW -->
	<li class="unit_item">

		<div class="message_a">
			<span class="greydd">{COM_ROW_OWNER_NAME}:</span>
			<!-- IF {COM_ROW_COM_AREA} == 'polls' -->
			<a href="./polls.php?id={COM_ROW_POLL_ID}#comments" title="{COM_ROW_POLL_TITLE}" class="greyd">
			<!-- ELSE -->
			<a href="{COM_ROW_PAGE_URL}#comments" title="{COM_ROW_PAGE_SHORTTITLE}" class="greyd">
			<!-- ENDIF -->
			
			<!-- IF {COM_ROW_TEXT|strlen($this)} > 3  -->
			{COM_ROW_TEXT|(cot_cutstring($this,144)}</a>
			<!-- ELSE -->
			...............
			<!-- ENDIF -->			
		</div>

		<p class="small">
			<!-- IF {PHP|date('d.m.Y')} == {COM_ROW_DATE_STAMP|cot_date('d.m.Y',$this)} -->
			<time datetime="{COM_ROW_DATE_STAMP|cot_date('d-m-Y\TH:i',$this)}" class="red"><strong>{PHP.skinlang.Today}</strong>,&nbsp;{COM_ROW_DATE_STAMP|cot_date('H:i',$this)}</time>
			<!-- ELSE -->
			<time datetime="{COM_ROW_DATE_STAMP|cot_date('d-m-Y\TH:i',$this)}" class="redd">{COM_ROW_DATE_STAMP|cot_date('d F',$this)},&nbsp;{COM_ROW_DATE_STAMP|cot_date('H:i',$this)}</time>
			<!-- ENDIF -->&nbsp;
			<!-- IF {COM_ROW_COM_AREA} == 'polls' -->
			<a href="./polls.php?id={COM_ROW_POLL_ID}#comments" title="{COM_ROW_POLL_TITLE}">
				{PHP.L.Poll}:&nbsp; {COM_ROW_POLL_TITLE|cot_cutstring($this,72)}
			</a>
			<!-- ELSE -->
			<a href="{COM_ROW_PAGE_URL}#comments" title="{COM_ROW_PAGE_SHORTTITLE}">
				{COM_ROW_PAGE_SHORTTITLE|cot_cutstring($this,200)}
			</a>
			<!-- ENDIF -->
		</p>
	</li>
<!-- END: COM_ROW -->
</ul>
<!-- BEGIN: NONE -->
{PHP.L.None}
<!-- END: NONE -->

<!-- IF {PAGE_TOP_PAGINATION} -->
<hr>
<ul class="cap pagination_arr clearfix">
	<li>{PAGE_TOP_PAGEPREV}&nbsp;</li>
	<li>{PAGE_TOP_CURRENTPAGE}/{PAGE_TOP_TOTALPAGES}</li>
	<li>&nbsp;{PAGE_TOP_PAGENEXT}</li>
</ul>
<!-- ENDIF -->
</div>


<!-- END: MAIN -->