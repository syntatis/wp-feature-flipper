import styles from './Fieldset.module.css';

export const Fieldset = ( { children, title, description } ) => {
	return (
		<div className={ styles.section }>
			{ title ? (
				<h2 className={ `title ${ styles.title }` }>{ title }</h2>
			) : null }
			{ description ? (
				<p className={ styles.description }>{ description }</p>
			) : null }
			<fieldset>
				<table className="form-table" role="presentation">
					<tbody>{ children }</tbody>
				</table>
			</fieldset>
		</div>
	);
};
