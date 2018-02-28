<?php
class Rice_View_Helper_MenuNew extends Zend_View_Helper_Navigation_Menu{
	
protected function _renderMenuNew(Zend_Navigation_Container $container,
                                   $ulClass,
                                   $ulClassDeepest,
                                   $indent,
                                   $minDepth,
                                   $maxDepth,
                                   $onlyActive,
                                   $ulId)
    {
        $html = '';

        // find deepest active
        if ($found = $this->findActive($container, $minDepth, $maxDepth)) {
            $foundPage = $found['page'];
            $foundDepth = $found['depth'];
        } else {
            $foundPage = null;
        }

        // create iterator
        $iterator = new RecursiveIteratorIterator($container,
                            RecursiveIteratorIterator::SELF_FIRST);
        if (is_int($maxDepth)) {
            $iterator->setMaxDepth($maxDepth);
        }

        // iterate container
        $prevDepth = -1;
        foreach ($iterator as $page) {
            $depth = $iterator->getDepth();
            $isActive = $page->isActive(true);
            if ($depth < $minDepth || !$this->accept($page)) {
                // page is below minDepth or not accepted by acl/visibilty
                continue;
            } else if ($onlyActive && !$isActive) {
                // page is not active itself, but might be in the active branch
                $accept = false;
                if ($foundPage) {
                    if ($foundPage->hasPage($page)) {
                        // accept if page is a direct child of the active page
                        $accept = true;
                    } else if ($foundPage->getParent()->hasPage($page)) {
                        // page is a sibling of the active page...
                        if (!$foundPage->hasPages() ||
                            is_int($maxDepth) && $foundDepth + 1 > $maxDepth) {
                            // accept if active page has no children, or the
                            // children are too deep to be rendered
                            $accept = true;
                        }
                    }
                }

                if (!$accept) {
                    continue;
                }
            }
            

            // make sure indentation is correct
            $depth -= $minDepth;
            $myIndent = $indent . str_repeat('        ', $depth);

                $style = $isActive ? 'style="display: block;"' : '  ';

            if ($depth > $prevDepth) {
                // start new ul tag
                if ($ulClass && $depth ==  0) {
                    $ulClass = ' class="' . $ulClass . '"';
                } else {
                    $ulClass = $ulClassDeepest?' class="'.$ulClassDeepest.'"':'';
                }


                $html .= $myIndent . '<ul' . $ulClass . ' id="'.$ulId.'"  '.$style.' >' . self::EOL;
            } else if ($prevDepth > $depth) {
                // close li/ul tags until we're at current depth
                for ($i = $prevDepth; $i > $depth; $i--) {
                    $ind = $indent . str_repeat('        ', $i);
                    $html .= $ind . '    </li>' . self::EOL;
                    $html .= $ind . '</ul>' . self::EOL;
                }
                // close previous li tag
                $html .= $myIndent . '    </li>' . self::EOL;
            } else {
                // close previous li tag
                $html .= $myIndent . '    </li>' . self::EOL;
            }
            // render li tag and page
            $liClass = "";
            $aux = explode(" ",$myIndent);
            if(count($aux)==1)
            $liClass = $isActive ? ' class="open"' : ' class=""';
            
            
            $html .= $myIndent . '    <li' . $liClass . '>' . self::EOL
                   . $myIndent . '        ' . $this->htmlify($page) . self::EOL;

            // store as previous depth for next iteration
            $prevDepth = $depth;
        }

        if ($html) {
            // done iterating container; close open ul/li tags
            for ($i = $prevDepth+1; $i > 0; $i--) {
                $myIndent = $indent . str_repeat('        ', $i-1);
                $html .= $myIndent . '    </li>' . self::EOL
                       . $myIndent . '</ul>' . self::EOL;
            }
            $html = rtrim($html, self::EOL);
        }
       

        return $html;
    }

    /**
     * Renders helper
     *
     * Renders a HTML 'ul' for the given $container. If $container is not given,
     * the container registered in the helper will be used.
     *
     * Available $options:
     *
     *
     * @param  Zend_Navigation_Container $container  [optional] container to
     *                                               create menu from. Default
     *                                               is to use the container
     *                                               retrieved from
     *                                               {@link getContainer()}.
     * @param  array                     $options    [optional] options for
     *                                               controlling rendering
     * @return string                                rendered menu
     */
    public function renderMenu(Zend_Navigation_Container $container = null,
                               array $options = array())
    {
        if (null === $container) {
            $container = $this->getContainer();
        }

        $options = $this->_normalizeOptions($options);
		if(!isset($options["ulClassDeepest"]))$options["ulClassDeepest"] = "";
        if ($options['onlyActiveBranch'] && !$options['renderParents']) {
            $html = $this->_renderDeepestMenu($container,
                                              $options['ulClass'],
                                              $options['indent'],
                                              $options['minDepth'],
                                              $options['maxDepth'],
                                              $options['ulId']);
        } else {
            $html = $this->_renderMenuNew($container,
                                       $options['ulClass'],
                                       $options['ulClassDeepest'],
                                       $options['indent'],
                                       $options['minDepth'],
                                       $options['maxDepth'],
                                       $options['onlyActiveBranch'],
            						   $options['ulId']);
        }

        return $html;
    }
    
    /**
     * Returns an HTML string containing an 'a' element for the given page if
     * the page's href is not empty, and a 'span' element if it is empty
     *
     * Overrides {@link Zend_View_Helper_Navigation_Abstract::htmlify()}.
     *
     * @param  Zend_Navigation_Page $page  page to generate HTML for
     * @return string                      HTML string for the given page
     */
    public function htmlify(Zend_Navigation_Page $page)
    {
    	// get label and title for translating
    	$label = $page->getLabel();
    	$title = $page->getTitle();
    	// translate label and title?
    	if ($this->getUseTranslator() && $t = $this->getTranslator()) {
    		if (is_string($label) && !empty($label)) {
    			$label = $t->translate($label);
    		}
    		if (is_string($title) && !empty($title)) {
    			$title = $t->translate($title);
    		}
    	}
    	$active = "";
    	if($page->getActive(false)){
    		$active = "";
    	}
    	// get attribs for element
    	$attribs = array(
    			'id'     => $page->getId(),
    			'title'  => $title,
    			'class'  => $page->getClass()." ".$active
    	);
    
    	// does page have a href?
    	if ($href = $page->getHref()) {
    		$element              = 'a';
    		$attribs['href']      = $href;
    		$attribs['target']    = $page->getTarget();
    		$attribs['accesskey'] = $page->getAccessKey();
    	} else {
    		$element = 'span';
    	}
    
    	// Add custom HTML attributes
    	$attribs = array_merge($attribs, $page->getCustomHtmlAttribs());
    
    	$icon = "";
    	if($page->__get("icon")){
    		$icon = "<i class=\"{$page->__get("icon")}\"></i>";
    	}
    	
    	return '<' . $element . $this->_htmlAttribs($attribs) . '>'
    			.$icon
    			."<span>" .  $this->view->escape($label) . "</span>"
    			. '</' . $element . '>';
    }
    
}