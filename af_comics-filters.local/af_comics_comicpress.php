<?php
class Af_Comics_ComicPress extends Af_ComicFilter {

    private $supportedList = array('littletinythings.com', 'smbc-comics.com');

    function supported() {
        return array("try to detect the wordpress comicpress plugin");
    }


    // MIT https://github.com/jmrware/LinkifyURL/blob/master/linkify.php
    function linkify($text) {
        $url_pattern = '/# Rev:20100913_0900 github.com\/jmrware\/LinkifyURL
        # Match http & ftp URL that is not already linkified.
          # Alternative 1: URL delimited by (parentheses).
          (\()                     # $1  "(" start delimiter.
          ((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]+)  # $2: URL.
          (\))                     # $3: ")" end delimiter.
        | # Alternative 2: URL delimited by [square brackets].
          (\[)                     # $4: "[" start delimiter.
          ((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]+)  # $5: URL.
          (\])                     # $6: "]" end delimiter.
        | # Alternative 3: URL delimited by {curly braces}.
          (\{)                     # $7: "{" start delimiter.
          ((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]+)  # $8: URL.
          (\})                     # $9: "}" end delimiter.
        | # Alternative 4: URL delimited by <angle brackets>.
          (<|&(?:lt|\#60|\#x3c);)  # $10: "<" start delimiter (or HTML entity).
          ((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]+)  # $11: URL.
          (>|&(?:gt|\#62|\#x3e);)  # $12: ">" end delimiter (or HTML entity).
        | # Alternative 5: URL not delimited by (), [], {} or <>.
          (                        # $13: Prefix proving URL not already linked.
            (?: ^                  # Can be a beginning of line or string, or
            | [^=\s\'"\]]          # a non-"=", non-quote, non-"]", followed by
            ) \s*[\'"]?            # optional whitespace and optional quote;
          | [^=\s]\s+              # or... a non-equals sign followed by whitespace.
          )                        # End $13. Non-prelinkified-proof prefix.
          ( \b                     # $14: Other non-delimited URL.
            (?:ht|f)tps?:\/\/      # Required literal http, https, ftp or ftps prefix.
            [a-z0-9\-._~!$\'()*+,;=:\/?#[\]@%]+ # All URI chars except "&" (normal*).
            (?:                    # Either on a "&" or at the end of URI.
              (?!                  # Allow a "&" char only if not start of an...
                &(?:gt|\#0*62|\#x0*3e);                  # HTML ">" entity, or
              | &(?:amp|apos|quot|\#0*3[49]|\#x0*2[27]); # a [&\'"] entity if
                [.!&\',:?;]?        # followed by optional punctuation then
                (?:[^a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]|$)  # a non-URI char or EOS.
              ) &                  # If neg-assertion true, match "&" (special).
              [a-z0-9\-._~!$\'()*+,;=:\/?#[\]@%]* # More non-& URI chars (normal*).
            )*                     # Unroll-the-loop (special normal*)*.
            [a-z0-9\-_~$()*+=\/#[\]@%]  # Last char can\'t be [.!&\',;:?]
          )                        # End $14. Other non-delimited URL.
        /imx';
        $url_replace = '$1$4$7$10$13<a href="$2$5$8$11$14">$2$5$8$11$14</a>$3$6$9$12';
        return preg_replace($url_pattern, $url_replace, $text);
    }
    /**
     * return if we are intended to handle this url
     */
    function process(&$article) {
        $link = $article["link"];

        if (!str_contains($article["guid"], "post_type=comic")) {
            return false;
        }
        if (!str_contains($article["link"], "/comic/")) {
            return false;
        }
        if (!str_contains($article["content"], "-150x150")) {
            return false;
        }
        // not 100% sure it's comicpress, but gonna try anyway?
        Debug::log("trying to parse as comicpress comic: " . $link, Debug::$LOG_EXTENDED);

        https://www.thethiefoftales.com/wp-content/uploads/2025/03/chap10_37-150x150.jpg
        $article["content"] = str_replace("-150x150", "", $article["content"]);
        $article["content"] = $this->linkify($article["content"]);

        return true;
    }
}
?>
