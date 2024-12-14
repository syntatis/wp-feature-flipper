import styles from './Details.module.scss';

export const Details = ( { summary, children, className = styles.root } ) => {
	return (
		<details className={ className }>
			<summary>{ summary }</summary>
			<div className={ styles.content }>{ children }</div>
		</details>
	);
};
