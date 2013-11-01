(function(C2C, $) {
  $(function() {
    $.get(C2C.meta_feed_url).done(function(data) {
      var $xml = $(data);
      var count = 0;
      var date;
      $xml.find("item").each(function() {
        var $this = $(this),
          item = {
            title: $this.find("title").text(),
            link: $this.find("link").text(),
            description: $this.find("description").text(),
            pubDate: $this.find("pubDate").text(),
            author: $this.find("author").text()
          }

        count++;
        if (count > 10) { return; }
        li_class = (count % 2) ? "even" : "odd";

        if (date != item["pubDate"]) {
          elems = item["pubDate"].split("-");
          datespan = "<span class=\"date\">" + elems[2] + "/" + elems[1] + "</span>";
        } else {
          datespan = "";
        }
        date = item["pubDate"];

        lang = item["description"].split(" , ")[5];

        author = item["author"].substring(item["author"].indexOf("(") - 1);

        $("#on_the_web_section_list").append(
          "<li class=\"" + li_class + "\">"
          + datespan
          + "<a href=\"" + item["link"] + "\" "
          + "hreflang=\"" + lang + "\""
          + ">" + item["title"] + "</a> "
          + "<span class=\"meta\">" + author + "</span>"
          + "</li>"
        );
      });
    });
  });
})(window.C2C = window.C2C || {}, jQuery);
