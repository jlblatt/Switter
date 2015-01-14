$(document).ready(
    function()
    {
	//hack set the onpopstate even to prevent chrome/safari from firing on page load
	setTimeout(
	    function()
	    {
		$(window).bind(
		    "popstate", 
		    function()
		    {
			$("#query").val(getURLParameter('query'));
			search();
		    }
		);
	    },
	    500
	);


	//ajax submits where supported, other reload page and let backend handle
	$("#switter").submit(
	    function(e)
	    {
		e.preventDefault();
		if(window.history.pushState) window.history.pushState({}, "Switter - Twitter Search - " + $("#query").val(), "./?query=" + encodeURIComponent($("#query").val()));
		search();
	    }
	);


	//url params on init load should be handled by the backend for degradation
	if(!getURLParameter('query')) 
	{
	    $("#query").focus();
	}


	//delegate
	$("#results")
	    .on("click", 
		".newsearch", 
		function(e)
		{
		    e.preventDefault();
		    $("#query").val($(this).text());
		    $("#switter").submit();
		}
	       );
	
    }
);

function search()
{
    if($("#query").val() == "") return;

    $("#query").blur();
    $("#results").empty().hide();
    $("#loading").show();

    $.getJSON(
	"cgi/twitter.cgi?" + encodeURIComponent($("#query").val())
    )
	.success(
            function(d)
            {
		for(var i = 0; i < d.results.length; i++)
		{
		    var re = new RegExp("(" + $("#query").val() + ")", "ig");

		    var tweetText = d.results[i].text
			.replace(re, "<strong>$1</strong>")
			.replace(/(http[s]?\:\/\/[^\s\<]+)/g, '<a target="_blank" href="$1">$1</a>')
			.replace(/\#([\w]+)/g, '<a class="newsearch" href="./?query=%23$1">#$1</a>')
			.replace(/\@([\w]+)/g, '<a class="newsearch" href="./?query=%40$1">@$1</a>');

		    $("#results").append(
			'<div class="tweet">' + 
			    '<div class="profile-image-container"><a target="_blank" href="http://twitter.com/#!/' + d.results[i].from_user + '"><img class="profile-image" src="' + d.results[i].profile_image_url + '" /></a></div>' + 
			    '<div class="timestamp">' + d.results[i].created_at.replace(/\+\d+$/, "") + '</div>' + 
			    '<div class="user"><a target="_blank" href="http://twitter.com/#!/' + d.results[i].from_user + '">' + d.results[i].from_user_name + '</a> (@' + d.results[i].from_user + ')</div>' + 
			    '<div class="text">' + tweetText + '</div>' + 
			    '<div class="permalink"><a target="_blank" href="http://twitter.com/#!/' + d.results[i].from_user + '/status/' + d.results[i].id + '">Permalink &raquo;</a></div>' + 
			    '<div class="clear"></div>' + 
			    '</div>'
		    );
		}

		$("#results").append('<div id="search-meta">Search completed in ' + d.completed_in + ' seconds.</div>').show();
		$("#loading").hide();
	    }
	)

	.error(
            function(d)
            {

	    }
	)

	.complete(
            function(d)
            {

	    }
	);
}

function getURLParameter(name) { return decodeURIComponent((RegExp('[?|&]' + name + '=' + '(.+?)(&|#|;|$)').exec(location.search)||[,""])[1].replace(/\+/g, '%20'))||null; }
