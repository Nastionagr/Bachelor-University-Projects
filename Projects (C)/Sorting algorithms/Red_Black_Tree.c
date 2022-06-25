#define _CRT_SECURE_NO_WARNINGS
#include <stdio.h>
#include <stdlib.h>

#define BLACK 0
#define RED 1
#define PARENT(x) (TREE*)((x)->parent)
#define GRANDPARENT(x) (TREE*)(((x)->parent)->parent)
#define Runcle(x) (TREE*)((((x)->parent)->parent)->right) //right uncle
#define Luncle(x) (TREE*)((((x)->parent)->parent)->left) //left uncle

typedef struct tree
{
	int data;
	struct tree *left;
	struct tree *right;
	int colour;
	struct tree *parent;
} TREE;

TREE* insert_rbtree(int cislo, TREE **parent)
{
	TREE *new = (TREE *)malloc(sizeof(struct tree));

	new->data = cislo; //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	new->left = NULL; //the lowest level of the tree doesn't have children
	new->right = NULL; //the lowest level of the tree doesn't have children
	new->parent = *parent;

	if (*parent == NULL) new->colour = BLACK; //if it is the root of the tree
	else
	{
		new->colour = RED;
		if (cislo > (*parent)->data) (*parent)->right = new;
		else (*parent)->left = new;
	}

	return new;
}

int search_rbtree(TREE *ROOT, int cislo, TREE **parent)
{
	int existence = 0;
	TREE *seeker;
	seeker = ROOT;

	while (cislo != seeker->data)
	{
		if (cislo > seeker->data)
			if (seeker->right != NULL) seeker = seeker->right;
			else break;
		else
			if (seeker->left != NULL) seeker = seeker->left;
			else break;
	}

	if (cislo == seeker->data) existence++;

	*parent = seeker;
	
	if (existence == 0) return 1; //cislo wasn't found in the tree
	else return 0;
}

void recolor(TREE **node)
{
	if (PARENT(*node) != NULL) (*node)->colour = !((*node)->colour); //the root always stays BLACK
	if ((*node)->left != NULL) ((*node)->left)->colour = !(((*node)->left)->colour);
	if ((*node)->right != NULL) ((*node)->right)->colour = !(((*node)->right)->colour);
}

void LeftLeftCase(TREE **node, TREE **remember)
{
	TREE *helper;
	helper = (GRANDPARENT(*node))->parent;

	(GRANDPARENT(*node))->left = (PARENT(*node))->right;
	if ((PARENT(*node))->right != NULL) ((PARENT(*node))->right)->parent = (GRANDPARENT(*node));
	
	(PARENT(*node))->right = GRANDPARENT(*node);
	(PARENT(*node))->parent = ((PARENT(*node))->right)->parent;
	((PARENT(*node))->right)->parent = PARENT(*node);
	if (helper != NULL)
	if (helper->left == (PARENT(*node))->right) helper->left = PARENT(*node);
	else helper->right = PARENT(*node);

	(PARENT(*node))->colour = !((PARENT(*node))->colour);
	((PARENT(*node))->right)->colour = !(((PARENT(*node))->right)->colour);

	*remember = PARENT(*node);
}

void LeftRightCase(TREE **node, TREE **remember)
{
	TREE *helper;
	helper = (*node)->left;

	(GRANDPARENT(*node))->left = *node;
	(*node)->left = PARENT(*node);
	PARENT(*node) = PARENT((*node)->left);
	PARENT((*node)->left) = (*node);
	((*node)->left)->right = helper;
	if (((*node)->left)->right != NULL) PARENT(((*node)->left)->right) = (*node)->left;

	LeftLeftCase(&((*node)->left), remember);
}

void RightRightCase(TREE **node, TREE **remember)
{
	TREE *helper;
	helper = (GRANDPARENT(*node))->parent;

	(GRANDPARENT(*node))->right = (PARENT(*node))->left;
	if ((PARENT(*node))->left != NULL) ((PARENT(*node))->left)->parent = (GRANDPARENT(*node));

	(PARENT(*node))->left = GRANDPARENT(*node);
	(PARENT(*node))->parent = ((PARENT(*node))->left)->parent;
	((PARENT(*node))->left)->parent = PARENT(*node);
	if (helper != NULL)
		if (helper->right == (PARENT(*node))->left) helper->right = PARENT(*node);
		else helper->left = PARENT(*node);

	(PARENT(*node))->colour = !((PARENT(*node))->colour);
	((PARENT(*node))->left)->colour = !(((PARENT(*node))->left)->colour);

	*remember = PARENT(*node);
}

void RightLeftCase(TREE **node, TREE **remember)
{
	TREE *helper;
	helper = (*node)->right;

	(GRANDPARENT(*node))->right = *node;
	(*node)->right = PARENT(*node);
	PARENT(*node) = PARENT((*node)->right);
	PARENT((*node)->right) = (*node);
	((*node)->right)->left = helper;
	if (((*node)->right)->left != NULL) PARENT(((*node)->right)->left) = (*node)->right;

	RightRightCase(&((*node)->right), remember);
}

void improve_rbtree(TREE **x)
{
	while ((PARENT(*x) != NULL) && ((*x)->colour == RED) && ((PARENT(*x))->colour == (*x)->colour))
	{
		TREE * remember = PARENT(*x); //for the next step

		if (GRANDPARENT(*x) != NULL) //PARENT(*x) isn't the root of the tree
		{
			if (PARENT(*x) == (GRANDPARENT(*x))->left)
			{
				if ((Runcle(*x) == NULL) || ((Runcle(*x))->colour == BLACK))
				{
					if ((*x) == (PARENT(*x))->left) LeftLeftCase(x, &remember);
					else LeftRightCase(x, &remember);
				}
				else
				{
					recolor(&(GRANDPARENT(*x)));
					remember = GRANDPARENT(*x);
				}
			}
			else
			{
				if ((Luncle(*x) == NULL) || ((Luncle(*x))->colour == BLACK))
				{
					if ((*x) == (PARENT(*x))->right) RightRightCase(x, &remember);
					else RightLeftCase(x, &remember);
				}
				else
				{
					recolor(&(GRANDPARENT(*x)));
					remember = GRANDPARENT(*x);
				}
			}
		}
		else (PARENT(*x))->colour = BLACK; //PARENT(*x) is the root of the tree

		*x = remember; //moving up to the root and checking if everything is OK
	}
}

int implement_rbtree(TREE **ROOT, int cislo)
{
	TREE *parent, *new;
	int existence;

	if ((*ROOT) == NULL) (*ROOT) = insert_rbtree(cislo, ROOT);
	else
	{
		existence = search_rbtree((*ROOT), cislo, &parent); //return 0 if cislo is already in the tree and we don't have to insert it
		if (existence) //if we need to insert cislo 
		{
			new = insert_rbtree(cislo, &parent);
			improve_rbtree(&new);
	
			while (PARENT(*ROOT)) //returning the current root
			{
				*ROOT = PARENT(*ROOT);
			}
		}
		else return 0;
	}

	return 1;
}

void destroy_rb(TREE *ROOT)
{
	if (ROOT != NULL)
	{
		destroy_rb(ROOT->left);
		destroy_rb(ROOT->right);
		free(ROOT);
		ROOT = NULL;
	}
}