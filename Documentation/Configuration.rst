:navigation-title: Configuration
..  _configuration:

=============
Configuration
=============

After installation you can use the `lih:hyphenate` ViewHelper in your templates by imporitng the namespace. 

..  tabs::
    ..  group-tab:: Tagbased Syntax
        in the opening html-Tag of your template-file like this

        .. code-block:: html

            <html xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers"
                  xmlns:lih="http://typo3.org/ns/LIA/LiaHyphenator/ViewHelpers"
                  data-namespace-typo3-fluid="true"
            >

    ..  group-tab:: Inline Syntax
        Use this line in your template before using any Hyphenator-ViewHelper:

        .. code-block:: text

          {namespace lih=LIA/LiaHyphenator/ViewHelpers}

See also `Import ViewHelper namespaces<https://docs.typo3.org/permalink/t3coreapi:fluid-syntax-viewhelpers-import-namespaces>`__
