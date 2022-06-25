#define _CRT_SECURE_NO_WARNINGS
#include <stdio.h>
#include <stdlib.h>

#define LCHILD(x) (int)(2*(x)+1)
#define RCHILD(x) (int)(2*(x)+2)
#define PARENT(x) (int)(((x)-1)/2)
#define INFINITY -1 //the way (time to get to some node) can't decrease, so we will never reach this number 

typedef struct node {
	int weight; //current weight in the heap
	int x, y;
	int edge; //C,D,P - 1     H - 2
	struct node *previous; //pointer to the previous node (from which we reach this one)
} NODE;

typedef struct heap {
	int space;
	int size;
	NODE **data;
} HEAP;

typedef struct toll {
	int count;
	NODE *D, *P1, *P2, *P3, *P4, *P5;
} TOLL;

void swap(HEAP **tree, int index1, int index2)
{
	NODE *helper = ((*tree)->data)[index1];

	((*tree)->data)[index1] = ((*tree)->data)[index2];
	((*tree)->data)[index2] = helper;
}

void min_heap_init(HEAP **tree, int quantity)
{
	*tree = (HEAP *)malloc(sizeof(HEAP));
	(*tree)->space = quantity; //the max size of the whole table = size of the matrix
	(*tree)->size = 0;
	(*tree)->data = (NODE **)calloc((*tree)->space, sizeof(NODE *));
}

void destroy_min_heap(HEAP *tree)
{
	free(tree->data);
	free(tree);
}

void heapify_bottom_top(HEAP **tree, int index)
{
	int parent = PARENT(index); //if parent = 0 - it is the root of the tree

	if (parent >= 0 && (((*tree)->data)[index])->weight < (((*tree)->data)[parent])->weight) //child is smaller than existing parent
	{
		swap(tree, index, parent);
		heapify_bottom_top(tree, parent);
	}
}

void heapify_top_bottom(HEAP **tree, int index)
{
	int left = LCHILD(index), right = RCHILD(index), smaller;

	if (((*tree)->data)[left] == NULL) return; //if there is no left child, right one is not existing too
	else
	{
		if (((*tree)->data)[right] == NULL) //there is just left child
		{
			if ((((*tree)->data)[left])->weight < (((*tree)->data)[index])->weight) //left child is smaller than its parent
			{
				swap(tree, index, left);
				heapify_top_bottom(tree, left);
			}
		}
		else //there is both of the children
		{
			//finding the smaller of them
			if ((((*tree)->data)[left])->weight <= (((*tree)->data)[right])->weight) smaller = left;
			else smaller = right;

			if ((((*tree)->data)[smaller])->weight < (((*tree)->data)[index])->weight) //if current node is bigger than its child - swap them
			{
				swap(tree, index, smaller);
				heapify_top_bottom(tree, smaller);
			}
		}

	}
}

NODE* pop_mini(HEAP **tree)
{
	NODE *reminder = ((*tree)->data)[0]; //remembering the root of the tree

	if (*tree == NULL || ((*tree)->data)[0] == NULL) return NULL; //if there is no elements in the heap
	else
	{
		((*tree)->data)[0] = ((*tree)->data)[(*tree)->size - 1]; //the last node become the root of the tree
		((*tree)->data)[(*tree)->size - 1] = NULL; //deleting the last element
		(*tree)->size--; //lower the number of the elements in the heap table
		heapify_top_bottom(tree, 0); //recover the right order of the elements in the heap
	}

	return reminder;
}

void min_heap_insert(HEAP **tree, NODE *element)
{
	if ((*tree)->size < (*tree)->space) //if there is enought space in the heap table
	{
		((*tree)->data)[(*tree)->size] = element; //inserting to the last position
		heapify_bottom_top(tree, (*tree)->size); //recover the right order of the elements in the heap
		(*tree)->size++; //bigger size
	}
}

void create_way(NODE *last, int *count)
{
	(*count)++; //counting nodes on our way
	if (last != NULL) create_way(last->previous, count); //till it's not a start node
}

int* dijikstra(NODE ***map, int n, int m, NODE *start, NODE *finish)
{
	int x, y, count = 0;
	int *information; //array of the coordinates from the whole way
	HEAP *table;
	NODE *helper;

	//initialization of the table
	for (x = 0; x < n; x++)
		for (y = 0; y < m; y++)
		{
			(map[x][y])->previous = NULL;
			(map[x][y])->weight = INFINITY;
		}

	(map[start->x][start->y])->weight = start->edge; //weight of the starting node is the same as its edge

	min_heap_init(&table, n*m); //creating heap table
	min_heap_insert(&table, map[start->x][start->y]); //inserting start node to the table

	do
	{
		helper = pop_mini(&table); //picking up the smallest node (the root)
		if (helper == finish) break; //if it is our finish, we stop looking way

		if (0 <= (helper->x) - 1 && (map[(helper->x) - 1][helper->y])->edge != INFINITY) //adding left neighbour (if it exists) to the heap table
		{
			if ((map[(helper->x) - 1][helper->y])->weight == INFINITY) //if we didn't visit it before
			{
				(map[(helper->x) - 1][helper->y])->weight = (map[(helper->x) - 1][helper->y])->edge + helper->weight; //weight of the way to it + its own
				(map[(helper->x) - 1][helper->y])->previous = helper;
				min_heap_insert(&table, map[(helper->x) - 1][helper->y]);
			}
		}

		if ((helper->x) + 1 < n && (map[(helper->x) + 1][helper->y])->edge != INFINITY) //adding right neighbour (if it exists) to the heap table
		{
			if ((map[(helper->x) + 1][helper->y])->weight == INFINITY) //if we didn't visit it before
			{
				(map[(helper->x) + 1][helper->y])->weight = (map[(helper->x) + 1][helper->y])->edge + helper->weight; //weight of the way to it + its own
				(map[(helper->x) + 1][helper->y])->previous = helper;
				min_heap_insert(&table, map[(helper->x) + 1][helper->y]);
			}
		}

		if (0 <= (helper->y) - 1 && (map[helper->x][(helper->y) - 1])->edge != INFINITY) //adding up neighbour (if it exists) to the heap table
		{
			if ((map[helper->x][(helper->y) - 1])->weight == INFINITY) //if we didn't visit it before
			{
				(map[helper->x][(helper->y) - 1])->weight = (map[helper->x][(helper->y) - 1])->edge + helper->weight; //weight of the way to it + its own
				(map[helper->x][(helper->y) - 1])->previous = helper;
				min_heap_insert(&table, map[helper->x][(helper->y) - 1]);
			}
		}

		if ((helper->y) + 1 < m && (map[helper->x][(helper->y) + 1])->edge != INFINITY) //adding down neighbour (if it exists) to the heap table
		{
			if ((map[helper->x][(helper->y) + 1])->weight == INFINITY) //if we didn't visit it before
			{
				(map[helper->x][(helper->y) + 1])->weight = (map[helper->x][(helper->y) + 1])->edge + helper->weight; //weight of the way to it + its own
				(map[helper->x][(helper->y) + 1])->previous = helper;
				min_heap_insert(&table, map[helper->x][(helper->y) + 1]);
			}
		}
	} while (table->size != 0); //if there is some elements in the heap - continue

	if (helper != finish) return NULL; //if the previous loop had already stopped and we still didn't find our finish node

	destroy_min_heap(table);

	create_way(helper, &count); //counting nodes to the start one

	information = (int *)malloc(2 * count * sizeof(int)); //creating the table for (x;y) + general info about the way (time, number of the whole array)
	
	information[0] = helper->weight; //time of the whole way
	information[1] = 2 * count; //number of the whole array
	count = 2 * (count - 1); //number of the x&y

	do
	{
		//we start filling in the way from the finish node
		information[count] = helper->x;
		information[count+1] = helper->y;
		count -= 2;
		
		helper = helper->previous; //coming back to the node (from which we came to the current one)
	} while (helper != NULL); //till it's not the start node

	return information;
}

void destroy_matrix(NODE ***matrix, int n, int m)
{
	int x, y;

	for (x = 0; x < n; x++)
		for (y = 0; y < m; y++)
		{
			free(matrix[x][y]);
		}

	for (y = 0; y < n; y++) free(matrix[y]);
	
	free(matrix);
}

NODE*** create_map(char **mapa, int n, int m, TOLL **destination)
{
	int x, y;
	NODE ***matrix; //table of the nodes

	matrix = (NODE ***)malloc(n * sizeof(NODE **));
	for (y = 0; y < n; y++)
	{
		matrix[y] = (NODE **)malloc(m * sizeof(NODE *));
	}

	*destination = (TOLL *)malloc(sizeof(TOLL));
	(*destination)->count = 0; //the number of the princesses

	for (x = 0; x < n; x++)
		for (y = 0; y < m; y++)
		{
			//creating the new node
			matrix[x][y] = (NODE *)malloc(sizeof(NODE));
			(matrix[x][y])->previous = NULL;
			(matrix[x][y])->weight = INFINITY;
			(matrix[x][y])->x = x; //remembering its axis
			(matrix[x][y])->y = y; //remembering its axis
			
			switch (mapa[x][y])
			{
			case 'C':
				(matrix[x][y])->edge = 1;
				break;

			case 'H':
				(matrix[x][y])->edge = 2;
				break;

			case 'N':
				(matrix[x][y])->edge = INFINITY; //its edge is negative, so in the future we won't be able to cross it
				break;

			case 'D':
				(matrix[x][y])->edge = 1;
				(*destination)->D = matrix[x][y]; //remembering the dragon's location (its node)
				break;

			case 'P':
				(matrix[x][y])->edge = 1;
				(*destination)->count++; //enlarge the number of the princesses

				if ((*destination)->count == 1)
				{
					(*destination)->P1 = matrix[x][y]; //remembering the princess ¹1 location (its node)
					(*destination)->P2 = NULL;
					(*destination)->P3 = NULL;
					(*destination)->P4 = NULL;
					(*destination)->P5 = NULL;
				}
				else
				if ((*destination)->count == 2) (*destination)->P2 = matrix[x][y]; //princess ¹2
				else
				if ((*destination)->count == 3) (*destination)->P3 = matrix[x][y]; //princess ¹3
				else
				if ((*destination)->count == 4) (*destination)->P4 = matrix[x][y]; //princess ¹4
				else
				if ((*destination)->count == 5) (*destination)->P5 = matrix[x][y]; //princess ¹5

				break;

			default: 
			{
				printf("Error during scanning an input map.\n"); //the map is incorrect
				destroy_matrix(matrix, n, m);
				free(destination);
				return NULL;
			}

			}
		}

	return matrix;
}

int* unite_arrays(int *array1, int *array2)
{
	int *array;
	int i, i1 = 4, length1 = array1[1], length2 = array2[1] - 4; 
	//we unite the weight and the size of the 2 arrays (-2 'int' from the array2), also we delete beginning of the array2 (as it's the same as the finish of the arra1) (-2 'int')

	array = (int *)malloc((length1 + length2) * sizeof(int)); //creating of the new united array

	array[0] = array1[0] + array2[0] - 1; //complete weight of the both array1 & array2 (-1 because we visit the one node twice -> finish array1 = start array2)
	array[1] = length1 + length2; //size of the new united array

	for (i = 2; i < length1; i += 2) //coping (x;y) from the array1
	{
		array[i] = array1[i];
		array[i + 1] = array1[i + 1];
	}

	for (i = length1; i < length1 + length2; i += 2) //coping (x;y) from the array2
	{
		array[i] = array2[i1];
		array[i + 1] = array2[i1 + 1];
		i1+=2;
	}

	return array;
}

int* twist_coordinates(int *array)
{
	int *new, fresh, old = array[1] - 2;

	new = (int *)malloc(array[1] * sizeof(int)); //creating of the new twisted array

	//the main information remainds the same
	new[0] = array[0];
	new[1] = array[1];

	for (fresh = 2; fresh < array[1]; fresh += 2)
	{
		new[fresh] = array[old]; //x_start = x_end
		new[fresh + 1] = array[old + 1]; //y_start = y_end
		old -= 2;
	}

	return new;
}

void swapik(int *number1, int *number2)
{
	int temporary;

	temporary = *number1;
	*number1 = *number2;
	*number2 = temporary;
}

void permutation(int ***table, int *min, int *array, int start, int end, int **way)
{
	int i;

	if (start == end)
	{
		int time;
		
		time = table[array[0]][array[0]][0] + table[array[0]][array[1]][0] + table[array[1]][array[2]][0]; //time = sum of the weights for a way to each princesses

		if (end == 3) time += table[array[2]][array[3]][0]; //we permute 4 princesses

		if (end == 4) time += table[array[2]][array[3]][0] + table[array[3]][array[4]][0]; //we permute 5 princesses

		if (*min == 0 || time <= *min) //if min wasn't initialized or the new time is smaller than the previous one
		{
			*min = time; //remembering smaller time
			
			//uniting ways between princesses (and dragon) (depends on their number)
			if (end == 2)
			{
				int *helper;

				helper = unite_arrays(table[array[0]][array[0]], table[array[0]][array[1]]);
				*way = unite_arrays(helper, table[array[1]][array[2]]);
				free(helper);
			}

			if (end == 3)
			{
				int *helper1, *helper2;

				helper1 = unite_arrays(table[array[0]][array[0]], table[array[0]][array[1]]);
				helper2 = unite_arrays(helper1, table[array[1]][array[2]]);
				free(helper1);
				*way = unite_arrays(helper2, table[array[2]][array[3]]);
				free(helper2);
			}

			if (end == 4)
			{
				int *helper1, *helper2, *helper3;

				helper1 = unite_arrays(table[array[0]][array[0]], table[array[0]][array[1]]);
				helper2 = unite_arrays(helper1, table[array[1]][array[2]]);
				free(helper1);
				helper3 = unite_arrays(helper2, table[array[2]][array[3]]);
				free(helper2);
				*way = unite_arrays(helper3, table[array[3]][array[4]]);
				free(helper3);
			}
		}

		return;
	}

	for (i = start; i <= end; i++) //permute the numbers
	{
		swapik((array + i), (array + start));
		permutation(table, min, array, start + 1, end, way);
		swapik((array + i), (array + start));
	}
}

int* compare(int number, int *way1, int *way2, int *way3, int *way4, int *way5, int *way12, int *way13, int *way14, int *way15, int *way23, int *way24, int *way25, int *way34, int *way35, int *way45)
{
	int *way;

	switch (number) //table's size depends on the number of princesses
	{
	case 3:
	{
		int ***table, min = 0, order[3], i;
		int *way21, *way31, *way32;

		for (i = 0; i < 3; i++) //P1 = 0, P2 = 1, P3 = 2
		{
			order[i] = i;
		}

		table = (int ***)malloc(3 * sizeof(int **)); //table 3*3
		for (i = 0; i < 3; i++)
		{
			table[i] = (int **)malloc(3 * sizeof(int *));
		}

		//on the diagonal - ways from the dragon to each princesses
		table[0][0] = way1;
		table[1][1] = way2;
		table[2][2] = way3;

		//then ways between princesses
		table[0][1] = way12;
		table[0][2] = way13;
		table[1][2] = way23;

		way21 = twist_coordinates(way12);
		way31 = twist_coordinates(way13);
		way32 = twist_coordinates(way23);

		table[1][0] = way21;
		table[2][0] = way31;
		table[2][1] = way32;

		permutation(table, &min, order, 0, 2, &way); //finding min way

		free(way1);
		free(way2);
		free(way3);
		free(way12);
		free(way13);
		free(way23);
		free(way21);
		free(way31);
		free(way32);

		for (i = 0; i < 3; i++) free(table[i]);
		free(table);

		break;
	}

	case 4:
	{
		int ***table, min = 0, order[4], i;
		int *way21, *way31, *way41, *way32, *way42, *way43;

		for (i = 0; i < 4; i++) //P1 = 0, P2 = 1, P3 = 2, P4 = 3
		{
			order[i] = i;
		}

		table = (int ***)malloc(4 * sizeof(int **)); //table 4*4
		for (i = 0; i < 4; i++)
		{
			table[i] = (int **)malloc(4 * sizeof(int *));
		}
		
		//on the diagonal - ways from the dragon to each princesses
		table[0][0] = way1;
		table[1][1] = way2;
		table[2][2] = way3;
		table[3][3] = way4;

		//then ways between princesses
		table[0][1] = way12;
		table[0][2] = way13;
		table[0][3] = way14;
		table[1][2] = way23;
		table[1][3] = way24;
		table[2][3] = way34;

		way21 = twist_coordinates(way12);
		way31 = twist_coordinates(way13);
		way41 = twist_coordinates(way14);
		way32 = twist_coordinates(way23);
		way42 = twist_coordinates(way24);
		way43 = twist_coordinates(way34);

		table[1][0] = way21;
		table[2][0] = way31;
		table[3][0] = way41;
		table[2][1] = way32;
		table[3][1] = way42;
		table[3][2] = way43;

		permutation(table, &min, order, 0, 3, &way); //finding min way

		free(way1);
		free(way2);
		free(way3);
		free(way4);
		free(way12);
		free(way13);
		free(way14);
		free(way23);
		free(way24);
		free(way34);
		free(way21);
		free(way31);
		free(way41);
		free(way32);
		free(way42);
		free(way43);

		for (i = 0; i < 4; i++) free(table[i]);
		free(table);

		break;
	}

	case 5:
	{
		int ***table, min = 0, order[5], i;
		int *way21, *way31, *way41, *way51, *way32, *way42, *way52, *way43, *way53, *way54;

		for (i = 0; i < 5; i++) //P1 = 0, P2 = 1, P3 = 2, P4 = 3, P5 = 4
		{
			order[i] = i;
		}

		table = (int ***)malloc(5 * sizeof(int **)); //table 5*5
		for (i = 0; i < 5; i++)
		{
			table[i] = (int **)malloc(5 * sizeof(int *));
		}

		//on the diagonal - ways from the dragon to each princesses
		table[0][0] = way1;
		table[1][1] = way2;
		table[2][2] = way3;
		table[3][3] = way4;
		table[4][4] = way5;

		//then ways between princesses
		table[0][1] = way12;
		table[0][2] = way13;
		table[0][3] = way14;
		table[0][4] = way15;
		table[1][2] = way23;
		table[1][3] = way24;
		table[1][4] = way25;
		table[2][3] = way34;
		table[2][4] = way35;
		table[3][4] = way45;

		way21 = twist_coordinates(way12);
		way31 = twist_coordinates(way13);
		way41 = twist_coordinates(way14);
		way51 = twist_coordinates(way15);
		way32 = twist_coordinates(way23);
		way42 = twist_coordinates(way24);
		way52 = twist_coordinates(way25);
		way43 = twist_coordinates(way34);
		way53 = twist_coordinates(way35); 
		way54 = twist_coordinates(way45);

		table[1][0] = way21;
		table[2][0] = way31;
		table[3][0] = way41;
		table[4][0] = way51;
		table[2][1] = way32;
		table[3][1] = way42;
		table[4][1] = way52;
		table[3][2] = way43;
		table[4][2] = way53;
		table[4][3] = way54;

		permutation(table, &min, order, 0, 4, &way); //finding min way

		free(way1);
		free(way2);
		free(way3);
		free(way4);
		free(way5);
		free(way12);
		free(way13);
		free(way14);
		free(way15);
		free(way23);
		free(way24);
		free(way25);
		free(way34);
		free(way35);
		free(way45);
		free(way21);
		free(way31);
		free(way41);
		free(way51);
		free(way42);
		free(way52);
		free(way43);
		free(way53);
		free(way54);

		for (i = 0; i < 4; i++) free(table[i]);
		free(table);

		break;
	}

	default: 
	{
		printf("Error\n");
		return NULL;
	}

	}

	return way; //min way from the dragon to all of the princesses
}

int* transform(int *final)
{
	int *array, i, size = final[1] - 2; //deleting main information about the way (its weight & size)

	array = (int *)malloc(size * sizeof(int)); //creating modified output (also changing placing of x&y)
	for (i = 0; i < size; i += 2)
	{
		array[i] = final[i + 3]; // x = y_old
		array[i+1] = final[i + 2]; // y = x_old
	}
	
	free(final);

	return array;
}

int* zachran_princezne(char **mapa, int n, int m, int t, int *dlzka_cesty)
{
	int *coordinates = NULL, *final = NULL;
	NODE ***matrix;
	TOLL *destination;
	int *kill_D = NULL, *way = NULL;

	matrix = create_map(mapa, n, m, &destination); //creating map of nodes

	if (matrix == NULL) return NULL;

	kill_D = dijikstra(matrix, n, m, matrix[0][0], destination->D); //way from (0;0) to the dragon

	if (kill_D == NULL) //if we didn't find the way
	{
		printf("Dragon is protected with rocks.\n");
		free(kill_D);
		destroy_matrix(matrix, n, m);
		free(destination);
		return NULL;
	}

	if (kill_D[0] <= t) //if we have enough time to kill the dragon
	{
		switch (destination->count) //the way depends on the number of the princesses
		{
		case 1:
		{
			way = dijikstra(matrix, n, m, destination->D, destination->P1); //searching way to the P1
			
			if (way == NULL) //if the princess is surrounded with rocks
			{
				printf("Popolvar can't reach the princess.\n");
				free(way);
				free(kill_D);
				destroy_matrix(matrix, n, m);
				free(destination);
				return NULL;
			}

			break;
		}

		case 2:
		{
			int *way1, *way2, *way12;

			way1 = dijikstra(matrix, n, m, destination->D, destination->P1); //searching way to the P1
			if (way1 == NULL) //if the princess is surrounded with rocks
			{
				printf("Popolvar can't reach the princess.\n");
				free(way1);
				free(kill_D);
				destroy_matrix(matrix, n, m);
				free(destination);
				return NULL;
			}

			way2 = dijikstra(matrix, n, m, destination->D, destination->P2); //searching way to the P2
			if (way2 == NULL) //if the princess is surrounded with rocks
			{
				printf("Popolvar can't reach the princess.\n");
				free(way1);
				free(way2);
				free(kill_D);
				destroy_matrix(matrix, n, m);
				free(destination);
				return NULL;
			}

			way12 = dijikstra(matrix, n, m, destination->P1, destination->P2); //way between P1 & P2

			if (way1[0] <= way2[0]) way = unite_arrays(way1, way12); //if we can reach P1 faster
			else //if we can reach P2 faster
			{
				int *way21;
				way21 = twist_coordinates(way12); //way between P2 & P1
				way = unite_arrays(way2, way21);
				free(way21);
			}

			free(way1);
			free(way2);
			free(way12);

			break;
		}

		case 3:
		{
			int *way1, *way2, *way3, *way12, *way13, *way23;

			way1 = dijikstra(matrix, n, m, destination->D, destination->P1); //searching way to the P1
			if (way1 == NULL) //if the princess is surrounded with rocks
			{
				printf("Popolvar can't reach the princess.\n");
				free(way1);
				free(kill_D);
				destroy_matrix(matrix, n, m);
				free(destination);
				return NULL;
			}

			way2 = dijikstra(matrix, n, m, destination->D, destination->P2); //searching way to the P2
			if (way2 == NULL) //if the princess is surrounded with rocks
			{
				printf("Popolvar can't reach the princess.\n");
				free(way1);
				free(way2);
				free(kill_D);
				destroy_matrix(matrix, n, m);
				free(destination);
				return NULL;
			}

			way3 = dijikstra(matrix, n, m, destination->D, destination->P3); //searching way to the P3
			if (way3 == NULL) //if the princess is surrounded with rocks
			{
				printf("Popolvar can't reach the princess.\n");
				free(way1);
				free(way2);
				free(way3);
				free(kill_D);
				destroy_matrix(matrix, n, m);
				free(destination);
				return NULL;
			}

			way12 = dijikstra(matrix, n, m, destination->P1, destination->P2); //way between P1 & P2
			way13 = dijikstra(matrix, n, m, destination->P1, destination->P3); //way between P1 & P3
			way23 = dijikstra(matrix, n, m, destination->P2, destination->P3); //way between P2 & P3

			way = compare(3, way1, way2, way3, NULL, NULL, way12, way13, NULL, NULL, way23, NULL, NULL, NULL, NULL, NULL);
			break;
		}
			
		case 4:
		{
			int *way1, *way2, *way3, *way4, *way12, *way13, *way14, *way23, *way24, *way34;

			way1 = dijikstra(matrix, n, m, destination->D, destination->P1); //searching way to the P1
			if (way1 == NULL) //if the princess is surrounded with rocks
			{
				printf("Popolvar can't reach the princess.\n");
				free(way1);
				free(kill_D);
				destroy_matrix(matrix, n, m);
				free(destination);
				return NULL;
			}

			way2 = dijikstra(matrix, n, m, destination->D, destination->P2); //searching way to the P2
			if (way2 == NULL) //if the princess is surrounded with rocks
			{
				printf("Popolvar can't reach the princess.\n");
				free(way1);
				free(way2);
				free(kill_D);
				destroy_matrix(matrix, n, m);
				free(destination);
				return NULL;
			}

			way3 = dijikstra(matrix, n, m, destination->D, destination->P3); //searching way to the P3
			if (way3 == NULL) //if the princess is surrounded with rocks
			{
				printf("Popolvar can't reach the princess.\n");
				free(way1);
				free(way2);
				free(way3);
				free(kill_D);
				destroy_matrix(matrix, n, m);
				free(destination);
				return NULL;
			}

			way4 = dijikstra(matrix, n, m, destination->D, destination->P4); //searching way to the P4
			if (way4 == NULL) //if the princess is surrounded with rocks
			{
				printf("Popolvar can't reach the princess.\n");
				free(way1);
				free(way2);
				free(way3);
				free(way4);
				free(kill_D);
				destroy_matrix(matrix, n, m);
				free(destination);
				return NULL;
			}

			way12 = dijikstra(matrix, n, m, destination->P1, destination->P2); //way between P1 & P2
			way13 = dijikstra(matrix, n, m, destination->P1, destination->P3); //way between P1 & P3
			way14 = dijikstra(matrix, n, m, destination->P1, destination->P4); //way between P1 & P4
			way23 = dijikstra(matrix, n, m, destination->P2, destination->P3); //way between P2 & P3
			way24 = dijikstra(matrix, n, m, destination->P2, destination->P4); //way between P2 & P4
			way34 = dijikstra(matrix, n, m, destination->P3, destination->P4); //way between P3 & P4

			way = compare(4, way1, way2, way3, way4, NULL, way12, way13, way14, NULL, way23, way24, NULL, way34, NULL, NULL);
			break;
		}

		case 5:
		{
			int *way1, *way2, *way3, *way4, *way5, *way12, *way13, *way14, *way15, *way23, *way24, *way25, *way34, *way35, *way45;

			way1 = dijikstra(matrix, n, m, destination->D, destination->P1); //searching way to the P1
			if (way1 == NULL) //if the princess is surrounded with rocks
			{
				printf("Popolvar can't reach the princess.\n");
				free(way1);
				free(kill_D);
				destroy_matrix(matrix, n, m);
				free(destination);
				return NULL;
			}

			way2 = dijikstra(matrix, n, m, destination->D, destination->P2); //searching way to the P2
			if (way2 == NULL) //if the princess is surrounded with rocks
			{
				printf("Popolvar can't reach the princess.\n");
				free(way1);
				free(way2);
				free(kill_D);
				destroy_matrix(matrix, n, m);
				free(destination);
				return NULL;
			}

			way3 = dijikstra(matrix, n, m, destination->D, destination->P3); //searching way to the P3
			if (way3 == NULL) //if the princess is surrounded with rocks
			{
				printf("Popolvar can't reach the princess.\n");
				free(way1);
				free(way2);
				free(way3);
				free(kill_D);
				destroy_matrix(matrix, n, m);
				free(destination);
				return NULL;
			}

			way4 = dijikstra(matrix, n, m, destination->D, destination->P4); //searching way to the P4
			if (way4 == NULL) //if the princess is surrounded with rocks
			{
				printf("Popolvar can't reach the princess.\n");
				free(way1);
				free(way2);
				free(way3);
				free(way4);
				free(kill_D);
				destroy_matrix(matrix, n, m);
				free(destination);
				return NULL;
			}

			way5 = dijikstra(matrix, n, m, destination->D, destination->P5); //searching way to the P5
			if (way5 == NULL) //if the princess is surrounded with rocks
			{
				printf("Popolvar can't reach the princess.\n");
				free(way1);
				free(way2);
				free(way3);
				free(way4);
				free(way5);
				free(kill_D);
				destroy_matrix(matrix, n, m);
				free(destination);
				return NULL;
			}

			way12 = dijikstra(matrix, n, m, destination->P1, destination->P2); //way between P1 & P2
			way13 = dijikstra(matrix, n, m, destination->P1, destination->P3); //way between P1 & P3
			way14 = dijikstra(matrix, n, m, destination->P1, destination->P4); //way between P1 & P4
			way15 = dijikstra(matrix, n, m, destination->P1, destination->P5); //way between P1 & P5
			way23 = dijikstra(matrix, n, m, destination->P2, destination->P3); //way between P2 & P3
			way24 = dijikstra(matrix, n, m, destination->P2, destination->P4); //way between P2 & P4
			way25 = dijikstra(matrix, n, m, destination->P2, destination->P5); //way between P2 & P5
			way34 = dijikstra(matrix, n, m, destination->P3, destination->P4); //way between P3 & P4
			way35 = dijikstra(matrix, n, m, destination->P3, destination->P5); //way between P3 & P5
			way45 = dijikstra(matrix, n, m, destination->P4, destination->P5); //way between P4 & P5

			way = compare(5, way1, way2, way3, way4, way5, way12, way13, way14, way15, way23, way24, way25, way34, way35, way45);
			break;
		}

		default: 
		{
			printf("There is more than 5 princesses.");
			free(kill_D);
			destroy_matrix(matrix, n, m);
			free(destination);
			return NULL;
		}

		}
	}
	else //dragon flies away
	{
		printf("Popolvar doesn't have enought time to kill the dragon.\n");
		free(kill_D);
		destroy_matrix(matrix, n, m);
		free(destination);
		return NULL;
	}

	final = unite_arrays(kill_D, way); //union of the way (0;0)-dragon & dragon-all_princesses 

	free(way);
	free(kill_D);
	free(destination);
	destroy_matrix(matrix, n, m);

	*dlzka_cesty = final[1] - 2; //number of (x;y) without main information (whole weight & size of the array)
	coordinates = transform(final); //creating output of the right type
	
	return coordinates;
}

void starter(FILE *f)
{
	char **mapa;
	int i, test, dlzka_cesty = 0, cas = 0, *cesta;
	int n = 0, m = 0, t = 0;

	if (f) fscanf(f, "%d %d %d", &n, &m, &t); //reading from the file

	//reading the input map of chars
	mapa = (char**)malloc(n * sizeof(char*));
	for (i = 0; i < n; i++)
	{
		mapa[i] = (char*)malloc(m * sizeof(char));
		for (int j = 0; j < m; j++)
		{
			char policko = fgetc(f);
			if (policko == '\n') policko = fgetc(f);
			mapa[i][j] = policko;
		}
	}

	fclose(f);

	cesta = zachran_princezne(mapa, n, m, t, &dlzka_cesty); //calling the main algorithm

	//printing the output
	for (i = 0; i < dlzka_cesty; i += 2)
	{
		printf("%d %d\n", cesta[i], cesta[i + 1]);

		if (mapa[cesta[i + 1]][cesta[i]] == 'H') cas += 2;
		else cas += 1;
	}
	printf("Time of the whole rescue operation is %d.\n", cas);

	free(cesta);
	for (i = 0; i < n; i++) free(mapa[i]);
	free(mapa);

	return;
}

int main() //testing
{	
	int test;
	FILE* f;
	
	while (1)
	{
		printf("**************************************\n");
		printf("PRINT THE NUMBER OF THE TEST (1-13):\n");
		printf("0 - EXIT\n\n");
		scanf("%d", &test);
		printf("**************************************\n");

		switch (test)
		{
		case 0: return 0;

		case 1: f = fopen("test1.txt", "r"); starter(f); break;
		case 2: f = fopen("test2.txt", "r"); starter(f); break;
		case 3: f = fopen("test3.txt", "r"); starter(f); break;
		case 4: f = fopen("test4.txt", "r"); starter(f); break;
		case 5: f = fopen("test5.txt", "r"); starter(f); break;
		case 6: f = fopen("test6.txt", "r"); starter(f); break;
		case 7: f = fopen("test7.txt", "r"); starter(f); break;
		case 8: f = fopen("test8.txt", "r"); starter(f); break;
		case 9: f = fopen("test9.txt", "r"); starter(f); break;
		case 10: f = fopen("test10.txt", "r"); starter(f); break;
		case 11: f = fopen("test11.txt", "r"); starter(f); break;
		case 12: f = fopen("test12.txt", "r"); starter(f); break;
		case 13: f = fopen("test13.txt", "r"); starter(f); break;

		default: printf("You put the wrong number.\n");

		}
	}
		
	return 0;
}