#define _CRT_SECURE_NO_WARNINGS
#include <stdio.h>
#include <stdlib.h>
#include <time.h>
#include "AVL_Tree.c"
#include "Red_Black_Tree.c"
#include "Trash_hash.c"
#include "Hashing.c"

#define random(x) (rand()%(x))
#define TEST_XS 10000
#define TEST_S 50000
#define TEST_M 100000
#define TEST_L 500000
#define TEST_XL 1000000

int *create_array(int size)
{
	int *array;
	int i;

	array = (int *)malloc(size * sizeof(int));

	for (i = 0; i < size; i++) array[i] = i + 1; // filling in array
	
	for (i = 0; i < size; i++) // shuffle array
	{
		int temp = array[i];
		int index = random(size);

		array[i] = array[index];
		array[index] = temp;
	}
	
	return array;
}

void swap (int **data, int situation, int *data1, int *data2, int *data3, int *data4, int *data5, int *data6, int *data7, int *data8, int *data9, int *data10)
{
	switch (situation)
	{
	case 1:
		*data = data1;
		break;

	case 2:
		*data = data2;
		break;

	case 3:
		*data = data3;
		break;

	case 4:
		*data = data4;
		break;

	case 5:
		*data = data5;
		break;

	case 6:
		*data = data6;
		break;

	case 7:
		*data = data7;
		break;

	case 8:
		*data = data8;
		break;

	case 9:
		*data = data9;
		break;

	case 10: 
		*data = data10;
		break;

	default:
		printf("ERROR\n");
	}
}

int search_AVL(AvlTree ROOT, int cislo)
{
	AvlTree seeker;
	seeker = ROOT;

	while (cislo != seeker->key)
	{
		if (cislo > seeker->key)
			if (seeker->child[1] != NULL) seeker = seeker->child[1];
			else break;
		else
			if (seeker->child[0] != NULL) seeker = seeker->child[0];
			else break;
	}

	if (cislo == seeker->key) return 1;

	return 0;
}

void test_insert_AVL (int size, int *data1, int *data2, int *data3, int *data4, int *data5, int *data6, int *data7, int *data8, int *data9, int *data10)
{
	int repeat;
	float time = 0;

	for (repeat = 1; repeat <= 10; repeat++)
	{
		AvlTree tree = AVL_EMPTY;
		int index, *data;
		clock_t start, end, duration;

		swap(&data, repeat, data1, data2, data3, data4, data5, data6, data7, data8, data9, data10);

		start = clock();
		for (index = 0; index < size; index++)
		{
			avlInsert(&tree, data[index]);
		}
		avlSanityCheck(tree);
		end = clock();

		avlDestroy(tree);

		duration = end - start;
		time += duration;
	}

	printf("AVL_TREE:		%f  ms\n", time / 10);
}

void test_insert_RB (int size, int *data1, int *data2, int *data3, int *data4, int *data5, int *data6, int *data7, int *data8, int *data9, int *data10)
{
	int repeat;
	float time = 0;

	for (repeat = 1; repeat <= 10; repeat++)
	{
		TREE *ROOT = NULL;
		int index, *data;
		clock_t start, end, duration;

		swap(&data, repeat, data1, data2, data3, data4, data5, data6, data7, data8, data9, data10);

		start = clock();
		for (index = 0; index < size; index++)
		{
			int mistake = implement_rbtree(&ROOT, data[index]);
			if (mistake == 0) printf("Error during inserting in RED_BLACK_TREE!\n");
		}
		end = clock();

		destroy_rb(ROOT);

		duration = end - start;
		time += duration;
	}

	printf("RED_BLACK_TREE:		%f  ms\n", time / 10);
}

void test_insert_hash_chain (int size, int *data1, int *data2, int *data3, int *data4, int *data5, int *data6, int *data7, int *data8, int *data9, int *data10)
{
	int repeat;
	float time = 0;

	for (repeat = 1; repeat <= 10; repeat++)
	{
		Hashtable *ht = createHashtable();
		int index, *data;
		clock_t start, end, duration;

		swap(&data, repeat, data1, data2, data3, data4, data5, data6, data7, data8, data9, data10);

		start = clock();
		for (index = 0; index < size; index++)
		{
			int mistake = insertKeyIntoHashtable(ht, data[index]);
			if (mistake == 1 || mistake == 2) printf("Error during inserting in HASH_CHAIN!\n");
		}
		end = clock();

		freeHashtable(ht);
		free(ht);

		duration = end - start;
		time += duration;
	}

	printf("HASH_CHAIN:		%f  ms\n", time / 10);
}

void test_insert_hash_adr (int size, int *data1, int *data2, int *data3, int *data4, int *data5, int *data6, int *data7, int *data8, int *data9, int *data10)
{
	int repeat;
	float time = 0;

	for (repeat = 1; repeat <= 10; repeat++)
	{
		TABLE *table = NULL;
		int index, *data;
		clock_t start, end, duration;

		swap (&data, repeat, data1, data2, data3, data4, data5, data6, data7, data8, data9, data10);
		
		start = clock();
		for (index = 0; index < size; index++)
		{
			int mistake = insert_mash(&table, data[index]);
			if (mistake == 0) printf("Error during inserting in HASH_ADR!\n");
		}
		end = clock();

		destroy_mash(table);

		duration = end - start;
		time += duration;
	}

	printf("HASH_ADR:		%f  ms\n", time / 10);
}

void test_search_AVL(int *numbers, int size, int *data1, int *data2, int *data3, int *data4, int *data5, int *data6, int *data7, int *data8, int *data9, int *data10)
{
	int repeat;
	float time = 0, time1 = 0;

	for (repeat = 1; repeat <= 10; repeat++)
	{
		AvlTree tree = AVL_EMPTY;
		int index, *data;
		clock_t start, end, duration, start1, end1, duration1;

		swap(&data, repeat, data1, data2, data3, data4, data5, data6, data7, data8, data9, data10);

		for (index = 0; index < size; index++)
		{
			avlInsert(&tree, data[index]);
		}
		avlSanityCheck(tree);

		start = clock();
		for (index = 0; index < size; index++)
		{
			assert(avlSearch(tree, numbers[index]) == 1);
		}
		end = clock();

		start1 = clock();
		for (index = 0; index < size; index++)
		{
			int mistake = search_AVL(tree, numbers[index]);
			if (mistake == 0) printf("Error during searching in my_AVL_TREE!\n");
		}
		end1 = clock();

		avlDestroy(tree);

		duration = end - start;
		time += duration;

		duration1 = end1 - start1;
		time1 += duration1;
	}

	printf("AVL_TREE:		%f  ms\n", time / 10);
	printf("AVL_TREE_my:		%f  ms\n", time1 / 10);
}

void test_search_RB(int *numbers, int size, int *data1, int *data2, int *data3, int *data4, int *data5, int *data6, int *data7, int *data8, int *data9, int *data10)
{
	int repeat;
	float time = 0;

	for (repeat = 1; repeat <= 10; repeat++)
	{
		TREE *ROOT = NULL;
		int index, *data;
		clock_t start, end, duration;

		swap(&data, repeat, data1, data2, data3, data4, data5, data6, data7, data8, data9, data10);

		for (index = 0; index < size; index++)
		{
			int mistake = implement_rbtree(&ROOT, data[index]);
			if (mistake == 0) printf("Error during inserting in RED_BLACK_TREE!\n");
		}

		start = clock();
		for (index = 0; index < size; index++)
		{
			TREE *parent;
			int mistake = search_rbtree(ROOT, numbers[index], &parent); //return 0 if numbers[index] is in the tree
			if (mistake == 1) printf("Error during searching in RED_BLACK_TREE!\n");
		}
		end = clock();

		destroy_rb(ROOT);

		duration = end - start;
		time += duration;
	}

	printf("RED_BLACK_TREE:		%f  ms\n", time / 10);
}

void test_search_hash_chain(int *numbers, int size, int *data1, int *data2, int *data3, int *data4, int *data5, int *data6, int *data7, int *data8, int *data9, int *data10)
{
	int repeat;
	float time = 0;

	for (repeat = 1; repeat <= 10; repeat++)
	{
		Hashtable *ht = createHashtable();
		int index, *data;
		clock_t start, end, duration;

		swap(&data, repeat, data1, data2, data3, data4, data5, data6, data7, data8, data9, data10);

		for (index = 0; index < size; index++)
		{
			int mistake = insertKeyIntoHashtable(ht, data[index]);
			if (mistake == 1 || mistake == 2) printf("Error during inserting in HASH_CHAIN!\n");
		}

		start = clock();
		for (index = 0; index < size; index++)
		{
			Node *list = NULL;
			list = searchKey(ht, numbers[index]);
			if (list == NULL) printf("Error during searhcing in HASH_CHAIN!\n");
		}
		end = clock();

		freeHashtable(ht);
		free(ht);

		duration = end - start;
		time += duration;
	}

	printf("HASH_CHAIN:		%f  ms\n", time / 10);
}

void test_search_hash_adr(int *numbers, int size, int *data1, int *data2, int *data3, int *data4, int *data5, int *data6, int *data7, int *data8, int *data9, int *data10)
{
	int repeat;
	float time = 0;

	for (repeat = 1; repeat <= 10; repeat++)
	{
		TABLE *table = NULL;
		int index, *data;
		clock_t start, end, duration;

		swap(&data, repeat, data1, data2, data3, data4, data5, data6, data7, data8, data9, data10);

		for (index = 0; index < size; index++)
		{
			int mistake = insert_mash(&table, data[index]);
			if (mistake == 0) printf("Error during inserting in HASH_ADR!\n");
		}

		start = clock();
		for (index = 0; index < size; index++)
		{
			int mistake = search_mash(table, numbers[index]);
			if (mistake == 0) printf("Error during searching in HASH_ADR!\n");
		}
		end = clock();

		destroy_mash(table);

		duration = end - start;
		time += duration;
	}

	printf("HASH_ADR:		%f  ms\n", time / 10);
}

void insert_test(int size)
{
	int *data1, *data2, *data3, *data4, *data5, *data6, *data7, *data8, *data9, *data10;

	//10 arrays of the same size, but with differently ordered data
	data1 = create_array(size);
	data2 = create_array(size);
	data3 = create_array(size);
	data4 = create_array(size);
	data5 = create_array(size);
	data6 = create_array(size);
	data7 = create_array(size);
	data8 = create_array(size);
	data9 = create_array(size);
	data10 = create_array(size);

	printf("\n");
	printf("%d numbers were inserted.\n", size);
	printf("An average duration of the insertion in\n");

	//testing&comparing different structures with the same datas
	test_insert_AVL(size, data1, data2, data3, data4, data5, data6, data7, data8, data9, data10);
	test_insert_RB(size, data1, data2, data3, data4, data5, data6, data7, data8, data9, data10);
	test_insert_hash_chain(size, data1, data2, data3, data4, data5, data6, data7, data8, data9, data10);
	test_insert_hash_adr(size, data1, data2, data3, data4, data5, data6, data7, data8, data9, data10);

	free(data1);
	free(data2);
	free(data3);
	free(data4);
	free(data5);
	free(data6);
	free(data7);
	free(data8);
	free(data9);
	free(data10);
}

void search_test(int size)
{
	int *data1, *data2, *data3, *data4, *data5, *data6, *data7, *data8, *data9, *data10, *numbers;

	//10 arrays of the same size, but with differently ordered data
	//1 array with elements that we will be searching
	data1 = create_array(size);
	data2 = create_array(size);
	data3 = create_array(size);
	data4 = create_array(size);
	data5 = create_array(size);
	data6 = create_array(size);
	data7 = create_array(size);
	data8 = create_array(size);
	data9 = create_array(size);
	data10 = create_array(size);
	numbers = create_array(size);

	printf("\n");
	printf("%d numbers were inserted and searched.\n", size);
	printf("An average duration of the search function in\n");

	//testing&comparing different structures with the same datas
	test_search_AVL(numbers, size, data1, data2, data3, data4, data5, data6, data7, data8, data9, data10);
	test_search_RB(numbers, size, data1, data2, data3, data4, data5, data6, data7, data8, data9, data10);
	test_search_hash_chain(numbers, size, data1, data2, data3, data4, data5, data6, data7, data8, data9, data10);
	test_search_hash_adr(numbers, size, data1, data2, data3, data4, data5, data6, data7, data8, data9, data10);

	free(data1);
	free(data2);
	free(data3);
	free(data4);
	free(data5);
	free(data6);
	free(data7);
	free(data8);
	free(data9);
	free(data10);
	free(numbers);
}

int main()
{
	int test;

	while (1)
	{
		printf("\n");
		printf("****************************************************************************\n");
		printf("Choose the insertion test that you want to perform:\n");
		printf("(1) - TEST_XS   (2) - TEST_S   (3) - TEST_M   (4) - TEST_L   (5) - TEST_XL\n");
		printf("\n");
		printf("Or search test in data structures of this size:\n");
		printf("(6) - TEST_XS   (7) - TEST_S   (8) - TEST_M   (9) - TEST_L   (10) - TEST_XL\n");
		printf("\n");
		printf("Put 11 for EXIT\n");
		printf("****************************************************************************\n");
		printf("\n");

		scanf("%d", &test);

		switch (test)
		{
		case 1:
			insert_test(TEST_XS);
			break;

		case 2:
			insert_test(TEST_S);
			break;

		case 3:
			insert_test(TEST_M);
			break;

		case 4:
			insert_test(TEST_L);
			break;

		case 5:
			insert_test(TEST_XL);
			break;

		case 6:
			search_test(TEST_XS);
			break;

		case 7:
			search_test(TEST_S);
			break;

		case 8:
			search_test(TEST_M);
			break;

		case 9:
			search_test(TEST_L);
			break;

		case 10:
			search_test(TEST_XL);
			break;

		case 11: return 0;

		default:
			printf("You put the wrong number.\n");
		}
	}
	
	return 0;
}