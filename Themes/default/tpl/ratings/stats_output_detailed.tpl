<?xml version="1.0" encoding="UTF-8" ?>
{include 'ratings/functions.tpl'}
<document>
    <response open="private_handler" fn="_append_stats_detailed" />
    <content>
        <![CDATA[ <!-- > -->
        <div class="blue_container light_shadow blue_topbar lefttext" id="_detailed_rating_stats" style="position:absolute;left:0;float:left;z-index:9999;min-width:250px;width:auto;">
            {call name=format_rating_stats}
        </div>
        ]]>
    </content>
    <data>
        {$C.json_data}
    </data>
    <handler>
        <![CDATA[ <!--
        function _append_stats_detailed(content, data)
        {
          var _el = $(content);
          if($('#_detailed_rating_stats').length > 0)
            $('#_detailed_rating_stats').remove();

          $('li#stats_detailed_' + data['mid']).find('span.rating_stats').after(_el);
          $('#_detailed_rating_stats').on('mouseleave', function(event) {
            $('#_detailed_rating_stats').remove();
          });
          return(false);
        }
        --> ]]>
    </handler>
</document>
