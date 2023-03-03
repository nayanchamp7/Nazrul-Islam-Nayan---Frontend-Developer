/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
const { __ } = wp.i18n;

import React from 'react';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
 */
const {
    InspectorControls,
    BlockControls,
    useBlockProps,
    JustifyToolbar,
    PanelColorSettings
} = wp.blockEditor;

import {
    __experimentalLinkControl as LinkControl
} from '@wordpress/block-editor';

const { Fragment, useState, useEffect, useRef } = wp.element;

import {
    Panel,
    PanelBody,
    Popover,
    RangeControl,
    Button,
    ToolbarButton,
    ToolbarGroup,
    ToggleControl,
    Toolbar,
    Dashicon,
    Modal,
} from '@wordpress/components';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit(props) {

    const { attributes, setAttributes } = props;
    const { style } = useBlockProps();

    const {
        search_by,
    } = attributes;

    const [isSearch, setIsSearch] = useState(false);

    return (
        <Fragment >
            <div className='spx-editor-wrapper' {...useBlockProps()} >
                <h2>Hello SpaceX</h2>
            </div>
        </Fragment>
    );
}
