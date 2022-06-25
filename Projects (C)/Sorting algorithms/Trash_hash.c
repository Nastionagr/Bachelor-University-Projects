#define _CRT_SECURE_NO_WARNINGS
#include <stdio.h>
#include <stdlib.h>
#include <stdbool.h>

#define BIGNESS 8 //has to be the power of 2 - used at the beginning
#define P(x) (int)(((x)*(x) + (x))/2)

typedef struct list
{
	int key;
} LIST;

typedef struct hashtable
{
	int size;
	int space; //mark the quantity of already allocated space of data
	struct list **data;
} TABLE;

void init(TABLE **table)
{
	(*table) = (TABLE *)malloc(sizeof(struct hashtable));
	(*table)->size = BIGNESS;
	(*table)->space = 0;
	(*table)->data = (LIST **)calloc(BIGNESS, sizeof(struct list*));
}

void improve_mash(TABLE **table)
{
	LIST **new = NULL;
	int index, i, k = 1;

	((*table)->size) *= 2; //enlarge the (*table)->data

	new = (LIST **)calloc(((*table)->size), sizeof(struct list*)); //creation of a bigger table

	for (i = 0; i < ((*table)->size) / 2; i++)
	{
		if (((*table)->data)[i] != NULL) //coping information from the old (*table)->data
		{
			int key = (((*table)->data)[i])->key;
			if (key < 0) key = -key;

			index = key % ((*table)->size); //creating the new index, as the new table has bigger size
			while (new[index] != NULL) //looking for the empty block
			{
				index = (unsigned long)(key + P(k)) % ((*table)->size);
				k++;
			}

			new[index] = (LIST *)malloc(sizeof(struct list));
			(new[index])->key = (((*table)->data)[i])->key;
			free(((*table)->data)[i]);
		}
	}

	free((*table)->data);
	(*table)->data = new;
}

int insert_mash(TABLE **table, int information)
{
	int key = information;
	if (key < 0) key = -key;

	if (*table == NULL) init(table);

	int k = 1, index = key % ((*table)->size);

	while (((*table)->data)[index] != NULL) //looking for the empty block
	{
		if ((((*table)->data)[index])->key == information) //return 0 if we already has this information in our table
		{
			return 0;
		}
		index = (unsigned long)(key + P(k)) % ((*table)->size);
		k++;
	}

	((*table)->data)[index] = (LIST *)malloc(sizeof(struct list));
	(((*table)->data)[index])->key = information;
	((*table)->space)++;

	if (((*table)->space) >= 0.4 * ((*table)->size)) improve_mash(table); //checking if we have enough free space for the future information
	return 1;
}

int search_mash(TABLE *table, int head)
{
	int key = head;
	if (key < 0) key = -key;

	if (table == NULL) return 0;

	int k = 1, index = key % (table->size);

	while ((table->data)[index] != NULL)
	{
		if (((table->data)[index])->key == head) return 1;
		index = (unsigned long)(key + P(k)) % (table->size);
		k++;
	}

	return 0;
}

void destroy_mash(TABLE *table)
{
	int index;
	for (index = 0; index < table->size; index++)
	{
		if ((table->data)[index] != NULL)
		{
			free((table->data)[index]);
			(table->data)[index] = NULL;
		}
	}

	if (table->data != NULL)
	{
		free(table->data);
		table->data = NULL;
	}

	if (table != NULL)
	{
		free(table);
		table = NULL;
	}
}