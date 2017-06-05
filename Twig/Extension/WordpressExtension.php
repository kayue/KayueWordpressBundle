<?php

namespace Kayue\WordpressBundle\Twig\Extension;

use Kayue\WordpressBundle\Doctrine\WordpressEntityManager;
use Kayue\WordpressBundle\Entity\Post;
use Kayue\WordpressBundle\Entity\Term;
use Kayue\WordpressBundle\Entity\User;
use Kayue\WordpressBundle\Wordpress\Helper\AttachmentHelper;
use Kayue\WordpressBundle\Wordpress\ManagerRegistry;
use Kayue\WordpressBundle\Wordpress\Shortcode\ShortcodeChain;
use Twig_SimpleFilter;
use Twig_SimpleFunction;

class WordpressExtension extends \Twig_Extension
{
    /**
     * @var ManagerRegistry
     */
    protected $managerRegistry;

    /**
     * @var WordpressEntityManager
     */
    protected $manager;

    /**
     * @var ShortcodeChain
     */
    protected $shortcodeChain;

    /**
     * @var AttachmentHelper
     */
    protected $attachmentHelper;

    /**
     * @param ManagerRegistry  $managerRegistry
     * @param ShortcodeChain   $shortcodeChain
     * @param AttachmentHelper $attachmentHelper
     */
    public function __construct(
        ManagerRegistry $managerRegistry,
        ShortcodeChain $shortcodeChain,
        AttachmentHelper $attachmentHelper
    )
    {
        $this->managerRegistry = $managerRegistry;
        $this->manager = $managerRegistry->getManager();
        $this->shortcodeChain = $shortcodeChain;
        $this->attachmentHelper = $attachmentHelper;
    }

    public function getName()
    {
        return "wordpress";
    }

    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter('wp_autop', [$this, 'wpautop']),
            new Twig_SimpleFilter('wp_texturize', [$this, 'wptexturize']),
            new Twig_SimpleFilter('wp_shortcode', [$this, 'doShortcode']),
        );
    }

    public function getFunctions()
    {
        return array(
            new Twig_SimpleFunction('wp_switch_blog', [$this, 'switchBlog']),
            new Twig_SimpleFunction('wp_find_option_by', [$this, 'findOneOptionBy']),

            // Post related functions
            new Twig_SimpleFunction('wp_find_post_by', [$this, 'findOnePostBy']),
            new Twig_SimpleFunction('wp_find_post_metas_by', [$this, 'findPostMetasBy']),
            new Twig_SimpleFunction('wp_find_comments_by_post', [$this, 'findCommentsByPost']),
            new Twig_SimpleFunction('wp_find_attachments', [$this, 'findAttachmentsByPost']),
            new Twig_SimpleFunction('wp_find_attachment_by_id', [$this, 'findOneAttachmentById']),
            new Twig_SimpleFunction('wp_find_thumbnail', [$this, 'findThumbnail']),
            new Twig_SimpleFunction('wp_find_featured_image', [$this, 'findThumbnail']),
            new Twig_SimpleFunction('wp_get_attachment_url', [$this, 'getAttachmentUrl']),
            new Twig_SimpleFunction('wp_get_post_format', [$this, 'getPostFormatByPost']),

            // Terms related functions
            new Twig_SimpleFunction('wp_find_terms_by_post', [$this, 'findTermsByPost']),
            new Twig_SimpleFunction('wp_find_categories_by_post', [$this, 'findCategoriesByPost']),
            new Twig_SimpleFunction('wp_find_tags_by_post', [$this, 'findTagsByPost']),

            // User related functions
            new Twig_SimpleFunction('wp_find_user_meta_by', [$this, 'findOneUserMetaBy']),
            new Twig_SimpleFunction('wp_find_user_metas_by', [$this, 'findUserMetasBy']),
        );
    }

    public function switchBlog($id)
    {
        $this->managerRegistry->setCurrentBlogId($id);
        $this->manager = $this->managerRegistry->getManager();
    }

    public function restoreBlog()
    {
        $this->managerRegistry->restorePreviousBlog();
        $this->manager = $this->managerRegistry->getManager();
    }

    public function findOneOptionBy($criteria)
    {
        if (is_string($criteria)) {
            $criteria = [
                'name' => $criteria
            ];
        }

        return $this->manager->getRepository('KayueWordpressBundle:Option')->findOneBy($criteria);
    }

    /**
     * Post related functions
     */

    public function findOnePostBy($criteria)
    {
        if (is_int($criteria)) {
            $criteria = [
                'id' => $criteria
            ];
        } elseif (is_string($criteria)) {
            $criteria = [
                'slug' => $criteria
            ];
        }

        $criteria = array_merge($criteria, [
            'type' => 'post',
            'status' => 'publish',
        ]);

        return $this->manager->getRepository('KayueWordpressBundle:Post')->findOneBy($criteria);
    }

    public function findPostMetasBy($criteria)
    {
        $repository = $this->manager->getRepository('KayueWordpressBundle:PostMeta');

        if (func_get_arg(0) instanceof Post && is_string(func_get_arg(1))) {
            return $repository->getMetasByPost(func_get_arg(0), func_get_arg(1));
        }

        return $repository->findBy($criteria);
    }

    public function findCommentsByPost(Post $post)
    {
        return $this->manager->getRepository('KayueWordpressBundle:Comment')->findApproved($post);
    }

    public function findAttachmentsByPost(Post $post)
    {
        return $this->manager->getRepository('KayueWordpressBundle:Post')->findAttachmentsByPost($post);
    }

    public function findOneAttachmentById($id)
    {
        return $this->manager->getRepository('KayueWordpressBundle:Post')->findAttachmentById($id);
    }

    public function findThumbnail(Post $post)
    {
        return $this->attachmentHelper->findThumbnail($post);
    }

    public function getAttachmentUrl($post)
    {
        return $this->attachmentHelper->getAttachmentUrl($post);
    }

    public function getPostFormatByPost(Post $post)
    {
        /** @var $term Term[] */
        $term = $this->findTermsByPost($post, 'post_format');

        if (!empty($term)) {
            return str_replace('post-format-', '', $term[0]->getSlug());
        }

        return 'standard';
    }

    /**
     * Term related functions
     */

    public function findTermsByPost(Post $post, $taxonomy = null)
    {
        return $this->manager->getRepository('KayueWordpressBundle:Term')->findByPost($post, $taxonomy);
    }

    public function findCategoriesByPost(Post $post)
    {
        return $this->findTermsByPost($post, 'category');
    }

    public function findTagsByPost(Post $post)
    {
        return $this->findTermsByPost($post, 'post_tag');
    }

    /**
     * User related functions
     */

    public function findOneUserMetaBy($criteria)
    {
        if (func_get_arg(0) instanceof User && is_string(func_get_arg(1))) {
            $criteria = [
                'user' => func_get_arg(0),
                'key'  => func_get_arg(1),
            ];
        }

        return $this->manager->getRepository('KayueWordpressBundle:UserMeta')->findOneBy($criteria);
    }

    public function findUserMetasBy($criteria)
    {
        if ($criteria instanceof User) {
            $criteria = [
                'user' => $criteria
            ];
        }

        return $this->manager->getRepository('KayueWordpressBundle:UserMeta')->findBy($criteria);
    }

    /**
     * Replaces double line-breaks with paragraph elements.
     *
     * A group of regex replaces used to identify text formatted with newlines and
     * replace double line-breaks with HTML paragraph tags. The remaining
     * line-breaks after conversion become <<br />> tags, unless $br is set to '0'
     * or 'false'.
     *
     * @param  string $pee The text which has to be formatted.
     * @param  bool   $br  Optional. If set, this will convert all remaining line-breaks after paragraphing. Default true.
     * @return string Text which has been converted into correct paragraph tags.
     */
    public function wpautop($pee, $br = true)
    {
        $pre_tags = array();

        if ( trim($pee) === '' )
            return '';

        $pee = $pee . "\n"; // just to make things a little easier, pad the end

        if ( strpos($pee, '<pre') !== false ) {
            $pee_parts = explode( '</pre>', $pee );
            $last_pee = array_pop($pee_parts);
            $pee = '';
            $i = 0;

            foreach ($pee_parts as $pee_part) {
                $start = strpos($pee_part, '<pre');

                // Malformed html?
                if ($start === false) {
                    $pee .= $pee_part;
                    continue;
                }

                $name = "<pre wp-pre-tag-$i></pre>";
                $pre_tags[$name] = substr( $pee_part, $start ) . '</pre>';

                $pee .= substr( $pee_part, 0, $start ) . $name;
                $i++;
            }

            $pee .= $last_pee;
        }

        $pee = preg_replace('|<br />\s*<br />|', "\n\n", $pee);
        // Space things out a little
        $allblocks = '(?:table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|option|form|map|area|blockquote|address|math|style|p|h[1-6]|hr|fieldset|legend|section|article|aside|hgroup|header|footer|nav|figure|figcaption|details|menu|summary)';
        $pee = preg_replace('!(<' . $allblocks . '[^>]*>)!', "\n$1", $pee);
        $pee = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $pee);
        $pee = str_replace(array("\r\n", "\r"), "\n", $pee); // cross-platform newlines
        if ( strpos($pee, '<object') !== false ) {
            $pee = preg_replace('|\s*<param([^>]*)>\s*|', "<param$1>", $pee); // no pee inside object/embed
            $pee = preg_replace('|\s*</embed>\s*|', '</embed>', $pee);
        }
        $pee = preg_replace("/\n\n+/", "\n\n", $pee); // take care of duplicates
        // make paragraphs, including one at the end
        $pees = preg_split('/\n\s*\n/', $pee, -1, PREG_SPLIT_NO_EMPTY);
        $pee = '';
        foreach ( $pees as $tinkle )
            $pee .= '<p>' . trim($tinkle, "\n") . "</p>\n";
        $pee = preg_replace('|<p>\s*</p>|', '', $pee); // under certain strange conditions it could create a P of entirely whitespace
        $pee = preg_replace('!<p>([^<]+)</(div|address|form)>!', "<p>$1</p></$2>", $pee);
        $pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee); // don't pee all over a tag
        $pee = preg_replace("|<p>(<li.+?)</p>|", "$1", $pee); // problem with nested lists
        $pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
        $pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);
        $pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)!', "$1", $pee);
        $pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);
        if ($br) {
            $pee = preg_replace_callback('/<(script|style).*?<\/\\1>/s', function ($matches) {
                // newline preservation help function for wpautop
                return str_replace("\n", "<WPPreserveNewline />", $matches[0]);
            }, $pee);
            $pee = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $pee); // optionally make line breaks
            $pee = str_replace('<WPPreserveNewline />', "\n", $pee);
        }
        $pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*<br />!', "$1", $pee);
        $pee = preg_replace('!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!', '$1', $pee);
        $pee = preg_replace( "|\n</p>$|", '</p>', $pee );

        if ( !empty($pre_tags) )
            $pee = str_replace(array_keys($pre_tags), array_values($pre_tags), $pee);

        return $pee;
    }

    /**
     * Replaces common plain text characters into formatted entities
     *
     * As an example,
     * <code>
     * 'cause today's effort makes it worth tomorrow's "holiday"...
     * </code>
     * Becomes:
     * <code>
     * &#8217;cause today&#8217;s effort makes it worth tomorrow&#8217;s &#8220;holiday&#8221;&#8230;
     * </code>
     * Code within certain html blocks are skipped.
     *
     * @param  string $text The text to be formatted
     * @return string The string replaced with html entities
     */
    public function wptexturize($text)
    {
        static $static_characters, $static_replacements, $dynamic_characters, $dynamic_replacements,
        $default_no_texturize_tags, $default_no_texturize_shortcodes;

        // No need to set up these static variables more than once
        if ( ! isset( $static_characters ) ) {
            /* translators: opening curly double quote */
            $opening_quote = '&#8220;';
            /* translators: closing curly double quote */
            $closing_quote = '&#8221;';

            /* translators: apostrophe, for example in 'cause or can't */
            $apos = '&#8217;';

            /* translators: prime, for example in 9' (nine feet) */
            $prime = '&#8242;';
            /* translators: double prime, for example in 9" (nine inches) */
            $double_prime = '&#8243;';

            /* translators: opening curly single quote */
            $opening_single_quote = '&#8216;';
            /* translators: closing curly single quote */
            $closing_single_quote = '&#8217;';

            /* translators: en dash */
            $en_dash = '&#8211;';
            /* translators: em dash */
            $em_dash = '&#8212;';

            $default_no_texturize_tags = array('pre', 'code', 'kbd', 'style', 'script', 'tt');
            $default_no_texturize_shortcodes = array('code');

            // if a plugin has provided an autocorrect array, use it
            if ("'" != $apos) { // Only bother if we're doing a replacement.
                $cockney = array( "'tain't", "'twere", "'twas", "'tis", "'twill", "'til", "'bout", "'nuff", "'round", "'cause" );
                $cockneyreplace = array( $apos . "tain" . $apos . "t", $apos . "twere", $apos . "twas", $apos . "tis", $apos . "twill", $apos . "til", $apos . "bout", $apos . "nuff", $apos . "round", $apos . "cause" );
            } else {
                $cockney = $cockneyreplace = array();
            }

            $static_characters = array_merge( array( '---', ' -- ', '--', ' - ', 'xn&#8211;', '...', '``', '\'\'', ' (tm)' ), $cockney );
            $static_replacements = array_merge( array( $em_dash, ' ' . $em_dash . ' ', $en_dash, ' ' . $en_dash . ' ', 'xn--', '&#8230;', $opening_quote, $closing_quote, ' &#8482;' ), $cockneyreplace );

            $dynamic = array();
            if ("'" != $apos) {
                $dynamic[ '/\'(\d\d(?:&#8217;|\')?s)/' ] = $apos . '$1'; // '99's
                $dynamic[ '/\'(\d)/'                   ] = $apos . '$1'; // '99
            }
            if ( "'" != $opening_single_quote )
                $dynamic[ '/(\s|\A|[([{<]|")\'/'       ] = '$1' . $opening_single_quote; // opening single quote, even after (, {, <, [
            if ( '"' != $double_prime )
                $dynamic[ '/(\d)"/'                    ] = '$1' . $double_prime; // 9" (double prime)
            if ( "'" != $prime )
                $dynamic[ '/(\d)\'/'                   ] = '$1' . $prime; // 9' (prime)
            if ( "'" != $apos )
                $dynamic[ '/(\S)\'([^\'\s])/'          ] = '$1' . $apos . '$2'; // apostrophe in a word
            if ( '"' != $opening_quote )
                $dynamic[ '/(\s|\A|[([{<])"(?!\s)/'    ] = '$1' . $opening_quote . '$2'; // opening double quote, even after (, {, <, [
            if ( '"' != $closing_quote )
                $dynamic[ '/"(\s|\S|\Z)/'              ] = $closing_quote . '$1'; // closing double quote
            if ( "'" != $closing_single_quote )
                $dynamic[ '/\'([\s.]|\Z)/'             ] = $closing_single_quote . '$1'; // closing single quote

            $dynamic[ '/\b(\d+)x(\d+)\b/'              ] = '$1&#215;$2'; // 9x9 (times)

            $dynamic_characters = array_keys( $dynamic );
            $dynamic_replacements = array_values( $dynamic );
        }

        // Transform into regexp sub-expression used in _wptexturize_pushpop_element
        // Must do this everytime in case plugins use these filters in a context sensitive manner
        $no_texturize_tags = '(' . implode('|', $default_no_texturize_tags) . ')';
        $no_texturize_shortcodes = '(' . implode('|', $default_no_texturize_shortcodes) . ')';

        $no_texturize_tags_stack = array();
        $no_texturize_shortcodes_stack = array();

        $textarr = preg_split('/(<.*>|\[.*\])/Us', $text, -1, PREG_SPLIT_DELIM_CAPTURE);

        foreach ($textarr as &$curl) {
            if ( empty( $curl ) )
                continue;

            // Only call _wptexturize_pushpop_element if first char is correct tag opening
            $first = $curl[0];
            if ('<' === $first) {
                $this->wptexturizePushpopElement($curl, $no_texturize_tags_stack, $no_texturize_tags, '<', '>');
            } elseif ('[' === $first) {
                $this->wptexturizePushpopElement($curl, $no_texturize_shortcodes_stack, $no_texturize_shortcodes, '[', ']');
            } elseif ( empty($no_texturize_shortcodes_stack) && empty($no_texturize_tags_stack) ) {
                // This is not a tag, nor is the texturization disabled static strings
                $curl = str_replace($static_characters, $static_replacements, $curl);
                // regular expressions
                $curl = preg_replace($dynamic_characters, $dynamic_replacements, $curl);
            }
            $curl = preg_replace('/&([^#])(?![a-zA-Z1-4]{1,8};)/', '&#038;$1', $curl);
        }

        return implode( '', $textarr );
    }

    /**
     * Search for disabled element tags. Push element to stack on tag open and pop
     * on tag close. Assumes first character of $text is tag opening.
     *
     * @access private
     * @since 2.9.0
     *
     * @param  string $text              Text to check. First character is assumed to be $opening
     * @param  array  $stack             Array used as stack of opened tag elements
     * @param  string $disabled_elements Tags to match against formatted as regexp sub-expression
     * @param  string $opening           Tag closing  character
     * @param  string $closing
     * @return object
     */
    public function wptexturizePushpopElement($text, &$stack, $disabled_elements, $opening = '<', $closing = '>')
    {
        // Check if it is a closing tag -- otherwise assume opening tag
        if (strncmp($opening . '/', $text, 2)) {
            // Opening? Check $text+1 against disabled elements
            if (preg_match('/^' . $disabled_elements . '\b/', substr($text, 1), $matches)) {
                /*
                 * This disables texturize until we find a closing tag of our type
                 * (e.g. <pre>) even if there was invalid nesting before that
                 *
                 * Example: in the case <pre>sadsadasd</code>"baba"</pre>
                 *          "baba" won't be texturize
                 */

                array_push($stack, $matches[1]);
            }
        } else {
            // Closing? Check $text+2 against disabled elements
            $c = preg_quote($closing, '/');
            if (preg_match('/^' . $disabled_elements . $c . '/', substr($text, 2), $matches)) {
                $last = array_pop($stack);

                // Make sure it matches the opening tag
                if ($last != $matches[1])
                    array_push($stack, $last);
            }
        }
    }

    /**
     * @param string $content Content to search for shortcodes
     *
     * @return string Content with shortcodes filtered out.
     */
    public function doShortcode($content)
    {
        return $this->shortcodeChain->process($content);
    }
}
