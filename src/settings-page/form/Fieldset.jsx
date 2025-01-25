import styles from './Fieldset.module.scss';

export const Fieldset = ( { children, title, description } ) => {
	return (
		<>
			{ title ? (
				<h2 className={ `title ${ styles.title }` }>{ title }</h2>
			) : null }
			{ description ? (
				<p className={ styles.description }>{ description }</p>
			) : null }
			<table
				className={ `form-table ${ styles.table }` }
				role="presentation"
			>
				<tbody>{ children }</tbody>
			</table>
		</>
	);
};
