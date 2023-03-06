/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
const { __ } = wp.i18n;

import React from 'react';
import ServerSideRender from "@wordpress/server-side-render";

// include scss file
import './style.scss';

const {
    InspectorControls,
    BlockControls,
    useBlockProps,
} = wp.blockEditor;

const { Fragment, useState, useEffect, useRef } = wp.element;

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

    // render the view
    function viewMode() {
        return(
            <>
                <ServerSideRender
                    block="spacex/craft"
                    attributes={attributes}
                />
            </>
        );
    }

    return (
        <Fragment >
            <div {...useBlockProps()}>
                { viewMode() }
            </div>
        </Fragment>
    );
}
