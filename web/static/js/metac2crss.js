(function($) {
  $(function() {
    $.get(rssurl, function(data) {
        var $xml = $(data);
        $xml.find("item").each(function() {
            var $this = $(this),
                item = {
                    title: $this.find("title").text(),
                    link: $this.find("link").text(),
                    description: $this.find("description").text(),
                    pubDate: $this.find("pubDate").text(),
                    author: $this.find("author").text()
            }
            $("#on_the_web_section_list").append("<li>" + item["title"] + "</li>");
        });
    });
  });
})(jQuery);
